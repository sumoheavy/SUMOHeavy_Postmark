<?php
/**
 * Postmark integration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@sumoheavy.com so we can send you a copy immediately.
 *
 * @category    SUMOHeavy
 * @package     SUMOHeavy_Postmark
 * @copyright   Copyright (c) 2012 SUMO Heavy Industries, LLC
 * @notice      The Postmark logo and name are trademarks of Wildbit, LLC
 * @license     http://www.opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class SUMOHeavy_Mail_Transport_Postmark extends Zend_Mail_Transport_Abstract
{
    /**
     * Postmark API Uri
     */
    const API_URI = 'https://api.postmarkapp.com/';

    /**
     * Limit of recipients per message in total.
     */
    const RECIPIENTS_LIMIT = 20;

    /**
     * Postmark API key
     *
     * @var string
     */
    protected $_apiKey = null;

    /**
     * HTTP client instance
     *
     * @var Zend_Http_Client
     */
    protected $_client = null;

    public function __construct($apiKey = '')
    {
        if (empty($apiKey)) {
            throw new Exception( __CLASS__ . ' requires API key' );
        }

        $this->_apiKey = $apiKey;
    }

    /**
     * Send request to Postmark service
     *
     * @link http://developer.postmarkapp.com/developer-build.html
     * @return stdClass
     */
    public function _sendMail()
    {
        $data = array(
            'From' => $this->getFrom(),
            'To' => $this->getTo(),
            'Cc' => $this->getCc(),
            'Bcc' => $this->getBcc(),
            'Subject' => $this->getSubject(),
            'ReplyTo' => $this->getReplyTo(),
            'HtmlBody' => $this->getBodyHtml(),
            'TextBody' => $this->getBodyText(),
            'tag' => $this->getTags(),
            'Attachments' => $this->getAttachments(),
        );

        $response = $this->prepareHttpClient('/email')
                         ->setMethod(Zend_Http_Client::POST)
                         ->setRawData(Zend_Json::encode($data))
                         ->request();

        return $this->_parseResponse($response);
    }

    /**
     * Get a http client instance
     *
     * @param string $path
     * @return Zend_Http_Client
     */
    protected function prepareHttpClient($path)
    {
        return $this->getHttpClient()->setUri(self::API_URI . $path);
    }

    /**
     * Returns http client object
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (null === $this->_client) {
            $this->_client = new Zend_Http_Client();

            $headers = array(
                'Accept' => 'application/json',
                'X-Postmark-Server-Token' => $this->_apiKey,
            );
            $this->_client->setMethod(Zend_Http_Client::GET)
                         ->setHeaders($headers);
        }

        return $this->_client;
    }

    /**
     * Parse response object and check for errors
     *
     * @param Zend_Http_Response $response
     * @return stdClass
     */
    protected function _parseResponse(Zend_Http_Response $response)
    {
        if ($response->isError()) {
            switch ($response->getStatus()) {
                case 401:
                    throw new RuntimeException('Postmark request error: Unauthorized - Missing or incorrect API Key header.');
                    break;
                case 422:
                    $error = Zend_Json::decode($response->getBody());
                    if(is_object($error)) {
                        throw new RuntimeException(sprintf('Postmark request error: Unprocessable Entity - API error code %s, message: %s', $error->ErrorCode, $error->Message));
                    } else {
                        throw new RuntimeException(sprintf('Postmark request error: Unprocessable Entity - API error code %s, message: %s', $error['ErrorCode'], $error['Message']));
                    }
                    break;
                case 500:
                    throw new RuntimeException('Postmark request error: Postmark Internal Server Error');
                    break;
                default:
                    throw new RuntimeException('Unknown error during request to Postmark server');
            }
        }

        return Zend_Json::decode($response->getBody());
    }

    /**
     * Get mail From
     *
     * @return string
     */
    public function getFrom()
    {
        $headers = $this->_mail->getHeaders();
        $from = array();

        if (isset($headers['From'])) {
            foreach($headers['From'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $from[] = $val;
                }
            }
        }
        return implode(',', $from);
    }

    /**
     * Get mail To
     *
     * @return string
     */
    public function getTo()
    {
        $headers = $this->_mail->getHeaders();
        $to = array();

        if (isset($headers['To'])) {
            foreach($headers['To'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $to[] = $val;
                }
            }
        }
        return implode(',', $to);
    }

    /**
     * Get mail Cc
     *
     * @return string
     */
    public function getCc()
    {
        $headers = $this->_mail->getHeaders();
        $cc = array();

        if (isset($headers['Cc'])) {
            foreach($headers['Cc'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $cc[] = $val;
                }
            }
        }

        if (count($cc) > self::RECIPIENTS_LIMIT) {
            throw new RuntimeException('Exceeded Postmark Cc recipients limit per message');
        }
        return implode(',', $cc);
    }

    /**
     * Get mail Bcc
     *
     * @return string
     */
    public function getBcc()
    {
        $headers = $this->_mail->getHeaders();
        $bcc = array();

        if (isset($headers['Bcc'])) {
            foreach($headers['Bcc'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $bcc[] = $val;
                }
            }
        }
        if (count($bcc) > self::RECIPIENTS_LIMIT) {
            throw new RuntimeException('Exceeded Postmark Bcc recipients limit per message');
        }
        return implode(',', $bcc);
    }

    /**
     * Get mail Reply To
     *
     * @return string
     */
    public function getReplyTo()
    {
        $headers = $this->_mail->getHeaders();
        $replyTo = array();

        if (isset($headers['Reply-To'])) {
            foreach($headers['Reply-To'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $replyTo[] = $val;
                }
            }
        }
        return implode(',', $replyTo);
    }

    /**
     * Get mail subject
     *
     * @return string
     */
    public function getSubject()
    {
        if(function_exists('imap_utf8')) {
            return imap_utf8($this->_mail->getSubject());
        }
        return $this->_mail->getSubject();
    }

    /**
     * Get mail body - html
     *
     * @return string
     */
    public function getBodyHtml()
    {
        if ($this->_mail->getBodyHtml()) {
            $part = $this->_mail->getBodyHtml();
            $part->encoding = false;
            return $part->getContent();
        }
        return '';
    }

    /**
     * Get mail body - plain
     *
     * @return string
     */
    public function getBodyText()
    {
        if ($this->_mail->getBodyText()) {
            $part = $this->_mail->getBodyText();
            $part->encoding = false;
            return $part->getContent();
        }
        return '';
    }

    /**
     * Get mail Tag
     *
     * @return string
     */
    public function getTags()
    {
        $headers = $this->_mail->getHeaders();
        $tags = array();

        if (isset($headers['postmark-tag'])) {
            foreach($headers['postmark-tag'] as $key => $val) {
                if(empty($key) || $key != 'append') {
                    $tags[] = $val;
                }
            }
        }
        return implode(',', $tags);
    }

    /**
     * Get mail Attachments
     *
     * @return array
     */
    public function getAttachments()
    {
        $attachments = array();
        if($this->_mail->hasAttachments) {
            $parts = $this->_mail->getParts();

            if(is_array($parts)) {
                $i = 0;
                foreach($parts as $part) {
                    $attachments[$i] = array(
                        'ContentType' => $part->type,
                        'Name' => $part->filename,
                        'Content' => $part->getContent(),
                    );
                    $i++;
                }
            }
        }

        return $attachments;
    }
}
