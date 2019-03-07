<?php

namespace Drupal\custom\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\HeaderBag;
use Drupal\node\Entity\NodeType;


/**
 * Check page node and output json data according to the input given.
 * 
 */
class CheckPage extends ControllerBase {
  public function checkpage($apikey,$nid) {
    $output = array(
      'type' => '',
      'status'=>false,
      'data' => '',
    );

    $node_url = \Drupal::request()->getpathInfo();
    $path_arr  = explode('/',$node_url);    
    $site_apikey = \Drupal::config('custom.settings')->get('siteapikey');
    
    //Checking the parameters are present in the URL
    if($path_arr[2]!="" && $path_arr[3]!="") {
    
        //Assigning node id and keyvalue to variables 
	$apikey = $path_arr[2];      
        $nid = $path_arr[3];
	
	//Loading the node values 
	$node = \Drupal\node\Entity\Node::load($nid);
	
	//This variable Stores the node type 
	$node_type = $node->type->entity->label();
	
	//Checking the similarity for apikey and parameter and node type to print json format of the page node
      if ($apikey == $site_apikey && $node_type == 'Basic page') {
      
         //Calling Drupal's serializer service  to get the page content decoding
	$serializer = \Drupal::service('serializer');
	
	//Serialized data assign to  $data variable in output
        $data = $serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
       $output['data'] = $data;
       $output['type'] = $node_type;
       $output['status'] = true;
	
	//Return result as json format
	return new JsonResponse($output);
	
      }elseif( $apikey == $site_apikey && $node_type != 'Basic page'){
      
	$output['type'] = 'not a page';
       $output['status'] = false;
       $output['data'] = 'Access Denied';
      
      //Print Access denied result if  node is not page
       return new JsonResponse($output);
       
      }elseif( $apikey != $site_apikey && $node_type != 'Basic page')
       
       $output['type'] = 'not a page';
       $output['status'] = false;
       $output['data'] = 'Access Denied';
	
	 //Print Access denied result if  node is not page and key is not siteapikey
	return new JsonResponse($output);
	
    }else {
    
       $output['type'] = 'not a page';
       $output['status'] = false;
       $output['data'] = 'Access Denied';
       
       return new JsonResponse($output);
    } 
  }
  
}

