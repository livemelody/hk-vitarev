<?php
/**
 * Author : Ebizmarts <info@ebizmarts.com>
 * Date   : 8/14/14
 * Time   : 6:48 PM
 * File   : Message.php
 * Module : Ebizmarts_Mandrill
 */
class Mandrill_Message extends Mandrill_Mandrill
{
    protected $_attachments = array();
    protected $_bcc = array();
    protected $_bodyText = false;
    protected $_bodyHtml = false;
    protected $_subject = null;
    protected $_from = null;
    protected $_to = array();
    protected $_headers = array();


    public function createAttachment($body,
                                     $mimeType    = Zend_Mime::TYPE_OCTETSTREAM,
                                     $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
                                     $encoding    = Zend_Mime::ENCODING_BASE64,
                                     $filename    = null)
    {
        $att = array('type' => $mimeType,'name' => $filename,'content'=> base64_encode($body));
        array_push($this->_attachments,$att);
    }
    public function log($m)
    {
        $storeId = Mage::app()->getStore()->getId();
        if(Mage::getStoreConfig(Ebizmarts_Mandrill_Model_System_Config::ENABLE_LOG,$storeId))
        {
            Mage::log($m,Zend_Log::INFO,'Mandrill.log');
        }
    }
    public function getAttachments()
    {
        return $this->_attachments;
    }
    public function addBcc($bcc)
    {
        $storeId = Mage::app()->getStore()->getId();
        if(is_array($bcc))
        {
            foreach($bcc as $email)
            {
                array_push($this->_bcc,$email);
            }
        }
        else
        {
            array_push($this->_bcc,$bcc);
        }
    }
    public function getBcc()
    {
        return $this->_bcc;
    }
    public function addTo($email, $name='')
    {
        if (!is_array($email)) {
            $email = array($name => $email);
        }

        foreach ($email as $n => $recipient) {
            $this->_to[] = $recipient;
        }

        return $this;
    }
    public function getTo()
    {
        return $this->_to;
    }

    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_bodyHtml = $html;
    }
    public function getBodyHtml()
    {
        return $this->_bodyHtml;
    }
    public function setBodyText($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_bodyText = $txt;
    }
    public function getBodyText()
    {
        return $this->_bodyText;
    }
    public function setSubject($subject)
    {
        if ($this->_subject === null) {
            $subject = $this->_filterOther($subject);
            $this->_subject = $subject;
        }
        return $this;
    }
    public function getSubject()
    {
        return $this->_subject;
    }
    public function setFrom($email, $name = null)
    {

        $email = $this->_filterEmail($email);
//        $name  = $this->_filterName($name);
        $this->_from = $email;
//        $this->_storeHeader('From', $this->_formatAddress($email, $name), true);

        return $this;
    }
    public function getFrom()
    {
        return $this->_from;
    }
    protected function _filterEmail($email)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => '',
            ','  => '',
            '<'  => '',
            '>'  => '',
        );

        return strtr($email, $rule);
    }

    /**
     * Filter of name data
     *
     * @param string $name
     * @return string
     */
    protected function _filterName($name)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => "'",
            '<'  => '[',
            '>'  => ']',
        );

        return trim(strtr($name, $rule));
    }
    protected function _filterOther($data)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
        );

        return strtr($data, $rule);
    }
    public function setReplyTo($email, $name = null)
    {
        $email = $this->_filterEmail($email);
        $name  = $this->_filterName($name);
        $this->_headers[] = array('Reply-To'=>sprintf('%s <%s>',$name,$email));
        return $this;
    }
    public function addHeader($name, $value, $append = false)
    {
        $prohibit = array('to', 'cc', 'bcc', 'from', 'subject',
            'reply-to', 'return-path',
            'date', 'message-id',
        );
        if (in_array(strtolower($name), $prohibit)) {
            /**
             * @see Zend_Mail_Exception
             */
            #require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('Cannot set standard header from addHeader()');
        }

        $this->_header[] = array($name=>$value);

        return $this;
    }
    public function getHeaders()
    {
        return $this->_headers;
    }

}