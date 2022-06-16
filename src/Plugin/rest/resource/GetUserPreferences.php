<?php
namespace Drupal\ccf_user_preferences\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\user\Entity\User;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\jwt\Transcoder\JwtTranscoderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\ccf_user_preferences\Entity\CcfUserPreferences;

/**
 * Provides REST API for User preferences Details based
 * on URL.
 *
 * @RestResource(
 *   id = "get_user_preferences_rest_resource",
 *   label = @Translation("Get User Preferences"),
 *   uri_paths = {
 *     "canonical" = "/api/get_user_preferences",
 *     "create"    = "/api/add_user_preferences"
 *   }
 * )
 */
class GetUserPreferences extends ResourceBase
{
  /**
   * The request object for redirect.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   *   The request object.
   */
  protected $request;

  /**
   * The JWT Transcoder service.
   *
   * @var \Drupal\jwt\Transcoder\JwtTranscoderInterface
   */
  protected $transcoder;

  /**
   * Constructor function.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param \Drupal\jwt\Transcoder\JwtTranscoderInterface $transcoder
   *   The JWT Transcoder service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition,
    array $serializer_formats, LoggerInterface $logger, Request $request, JwtTranscoderInterface $transcoder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition,$serializer_formats,$logger);
    $this->request = $request;
    $this->transcoder = $transcoder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,array
  $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('jwt.transcoder')
    );
  }

  /**
   * Array mapping keys & fields
   *
   * @return void
   */
  public $mapper = ['tagsDietary' => 'field_dietary' ,'tagsExclusion' => 'field_exclusion','tagsMeal' => 'field_meals'];
  /**
   * Responds to entity GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Returning rest resource.
   */
  public function get()
  {
    $data = [];
    $user_preferences = [];
    try {
      if ($this->request->query->has('jwt_token'))
      {
        $jwt_token     = $this->request->query->get('jwt_token');
        if (!empty($jwt_token)) {
          $decoded_token = $this->transcoder->decode($jwt_token);
          if(is_object($decoded_token)) {
            $decoded_token_array =json_decode(json_encode($decoded_token->getPayload()),true);
            $uid = $decoded_token_array['drupal']['uid'];
            $uid = 4041;
            if($uid) {
              // Get user preferences_entity_id
              $user = User::load($uid);
              if($user) {
                $user_preferences_id =  $user->get('field_user_preferences')->first();
                if($user_preferences_id) {
                  $user_preferences    = $user_preferences_id->get('entity')->getTarget()->getValue();
                  foreach($this->mapper as $nestle_key => $drupal_field_name) {
                    $data['preferences'][$nestle_key] = array_column($user_preferences->get($drupal_field_name)->getValue(),'value');
                  }
                }
              }
            }
          }
        }
      }
    }
    catch(\Exception $e) {
      $data['error'] = $e->getMessage();
    }
    return new ModifiedResourceResponse($data);
  }

  /**
   * Responds to entity POST requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Returning rest resource.
   */
  public function post($data)
  {
    $ccf = [];
    try {
      if(isset($data)) {
        $jwt_token = $data['jwt_token'];
        if (!empty($jwt_token)) {
          $decoded_token = $this->transcoder->decode($jwt_token);
          if(is_object($decoded_token)) {
            $decoded_token_array =json_decode(json_encode($decoded_token->getPayload()),true);
            $uid = $decoded_token_array['drupal']['uid'];
            $user_preferences = $data['preferences'];
            $uid = 4041;
            $user = User::load($uid);
            if($user) {
              $user_preferences_entity =  $user->get('field_user_preferences')->first();
              if($user_preferences_entity) {
                $user_preferences_id = $user_preferences_entity->getValue()['target_id'];
                $entity_update = CcfUserPreferences::load($user_preferences_id);
                foreach($this->mapper as $nestle_key => $drupal_field_name) {
                  if(!empty($user_preferences[$nestle_key])) {
                    $entity_update->set($drupal_field_name,$user_preferences[$nestle_key]);
                  }
                }
                $entity_update->save();
              }
              else {
                foreach($this->mapper as $nestle_key => $drupal_field_name) {
                  $ccf[$drupal_field_name] = $user_preferences[$nestle_key];
                }
                $ccf['type'] = 'ccf_user_preferences';
                $ccf['uid'] = 1;
                $ccf['status'] = 1;
                $entity_creation = CcfUserPreferences::create($ccf);
                $entity_creation->save();
                $user->set('field_user_preferences',$entity_creation->id());
                $user->save();
              }
            }
            $message['message'] = "Successfully Updated";
            return new ModifiedResourceResponse($message);
          }
        }
      }
    }
    catch(\Exception $e) {
      $message['error'] = $e->getMessage();
      return new ModifiedResourceResponse($message);
    }
  }
}

?>
