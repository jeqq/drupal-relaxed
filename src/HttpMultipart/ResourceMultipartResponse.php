<?php

/**
 * @file
 * Definition of Drupal\relaxed\ResourceMultipartResponse.
 */

namespace Drupal\relaxed\HttpMultipart;

use Drupal\relaxed\HttpMultipart\HttpFoundation\MultipartResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains data for serialization before sending the response.
 */
class ResourceMultipartResponse extends MultipartResponse {

  /**
   * {@inheritdoc}
   */
  public function prepare(Request $request)
  {
    // Fix the timeout error on replication.
    $this->headers->set('Connection', 'close');

    return parent::prepare($request);
  }

  /**
   * Sends content for the current web response.
   *
   * @return Response
   */
  public function sendContent() {
    // This fixes the "Malformed encoding found in chunked-encoding"
    // error message in curl and makes possible to get the correct response body.
    // @todo Figure out if this is the best way to fix the problem.
    $size = $this->getSize();
    echo "$size\r\n";
    parent::sendContent();
  }

  /**
   * Returns the length of all the parts in the response body.
   *
   * @return int
   */
  protected function getSize() {
    $size = 0;
    foreach ($this->parts as $part) {
      $content = $part->getContent();
      $output = "--{$this->boundary}" . "{$part->headers}" . $content;
      $size += strlen($output);
    }
    return $size;
  }
}
