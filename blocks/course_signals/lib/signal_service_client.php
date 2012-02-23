<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Block implementation
 * @package   course_signals
 * @copyright 2012 Moodlerooms inc. (http://moodlerooms.com)
 * @author    Darko Miletic <dmiletic@moodlerooms.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once('signal_service_classmap.php');


/**
 *
 * This code is taken from existing implementation to add missing WS-Security
 * password digest support
 * @author Darko Miletic <dmiletic@moodlerooms.com>
 *
 */
class course_signal_wsse_authheader extends SoapHeader {

    private $wss_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
    private $wsu_ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';

    function __construct($user, $pass) {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $nonce = mt_rand();
        $passdigest = base64_encode(
                                    pack('H*',
                                    sha1(
                                    pack('H*', $nonce) . pack('a*',$timestamp).
                                    pack('a*',$pass))));
        $anonce = base64_encode(pack('H*', $nonce));
        $auth = new stdClass();
        $auth->Username = new SoapVar($user, XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
        $auth->Password = new SoapVar('<ns2:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">'.$passdigest.'</ns2:Password>', XSD_ANYXML);
        $auth->Nonce    = new SoapVar('<ns2:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">'.$anonce.'</ns2:Nonce>', XSD_ANYXML);
        $auth->Created  = new SoapVar($timestamp, XSD_STRING, NULL, $this->wsu_ns, NULL, $this->wsu_ns);

        $username_token = new stdClass();
        $username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);

        $security_sv = new SoapVar(
        new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),
        SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);
        parent::__construct($this->wss_ns, 'Security', $security_sv, true);
    }
}

class course_signal_service_client extends SoapClient {
    /**
     * @var string
     */
    private $invalidargsmsg = null;
    /**
     * @var string
     */
    private $default_errmsg = null;
    /**
     * @var DOMDocument
     */
    private $xmlbeauty = null;
    /**
     * @var string
     */
    private $errormsg  = null;
    /**
     * @var bool
     */
    private $internalerror = false;
    /**
     * @var string
     */
    private $username = null;
    /**
     * @var string
     */
    private $password = null;

    /**
     *
     * Beautifies input XML
     * @param string $xml
     * @return string
     */
    private function beautify($xml) {
        $result = '';
        if (!empty($xml)) {
            libxml_use_internal_errors(false);
            if ($this->xmlbeauty->loadXML($xml, LIBXML_NONET)) {
              $result = $this->xmlbeauty->saveXML();
            }
        }
        return $result;
    }

    /**
     *
     * class ctor
     * @param string $wsdl
     * @param string $username
     * @param string $password
     * @param array  $params
     */
    public function __construct($wsdl, $username = null, $password = null, $params = array()) {
        $this->xmlbeauty = new DOMDocument();
        $this->xmlbeauty->formatOutput        = true;
        $this->xmlbeauty->preserveWhiteSpace  = true;
        $this->xmlbeauty->strictErrorChecking = false;
        $this->xmlbeauty->validateOnParse     = false;
        $this->default_errmsg = get_string('ok');
        $this->invalidargsmsg = get_string('invalidarguments', 'error');

        $parameters2 = array(
                               'soap_version'       => SOAP_1_1,
                               'compression'        => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE,
                               'encoding'           => 'UTF-8',
                               'trace'              => true,
                               'user_agent'         => 'PHP SOAP client',
                               'cache_wsdl'         => WSDL_CACHE_NONE,
                               'features'           => SOAP_SINGLE_ELEMENT_ARRAYS |
                                                       SOAP_USE_XSI_ARRAY_TYPE |
                                                       SOAP_WAIT_ONE_WAY_CALLS,
                               'exceptions'         => false,
                               'classmap'           => array('interventionResult'                   => 'interventionResult',
                                                             'getSignalForStudentLMSId'             => 'getSignalForStudentLMSId',
                                                             'getSignalForStudentLMSIdResponse'     => 'getSignalForStudentLMSIdResponse',
                                                             'getSignalForStudentSourcedID'         => 'getSignalForStudentSourcedID',
                                                             'getSignalForStudentSourcedIDResponse' => 'getSignalForStudentSourcedIDResponse'
                                                             )

        );

        $this->username = $username;
        $this->password = $password;
        $param = array_merge($parameters2,$params);
        parent::__construct($wsdl, $param);
    }

    /**
     *
     * Obtains a singal data for a student in a course
     * In case of no signal being present returns NULL
     * @param string $student_lmsid
     * @param string $sectionid
     * @return interventionResult
     */
    public function get_signal_for_studentlmsid($student_lmsid, $sectionid) {
        if (empty($student_sourcedid) || empty($sectionsourcedid)) {
            $this->internalerror = true;
            $this->errormsg = $this->invalidargsmsg;
            return null;
        }

        //delete any previous SOAP headers
        $this->__setSoapHeaders(null);
        if (!empty($this->username) && !empty($this->password)) {
            $wsse_header = new course_signal_wsse_authheader($this->username, $this->password);
            $this->__setSoapHeaders(array($wsse_header));
        }
        $params = new getSignalForStudentLMSId($student_lmsid, $sectionid);
        $result = $this->getSignalForStudentLMSId($params);
        $this->internalerror = is_soap_fault($result);
        if ($this->internalerror) {
            $this->errormsg = $result->faultstring;
        } else {
            $this->errormsg = $this->default_errmsg;
            $result = $result->StudentSignal;
        }
        return $result;
    }

    /**
    *
    * Obtains a singal data for a student in a course
    * In case of no signal being present returns NULL
    * @param string $student_sourcedid
    * @param string $sectionsourcedid
    * @return interventionResult
    */
    public function get_signal_for_studentsourcedid($student_sourcedid, $sectionsourcedid) {
        if (empty($student_sourcedid) || empty($sectionsourcedid)) {
            $this->internalerror = true;
            $this->errormsg = $this->invalidargsmsg;
            return null;
        }
                //delete any previous SOAP headers
        $this->__setSoapHeaders(null);
        if (!empty($this->username) && !empty($this->password)) {
            $wsse_header = new course_signal_wsse_authheader($this->username, $this->password);
            $this->__setSoapHeaders(array($wsse_header));
        }
        $params = new getSignalForStudentSourcedID($student_sourcedid, $sectionsourcedid);
        $result = $this->getSignalForStudentSourcedID($params);
        $this->internalerror = is_soap_fault($result);
        if ($this->internalerror) {
            $this->errormsg = $result->faultstring;
        } else {
            $this->errormsg = $this->default_errmsg;
            $result = $result->StudentSignal;
        }
        return $result;
    }

    /**
     * @return string
     */
    public function errormsg() {
        return $this->errormsg;
    }

    /**
     * @return bool
     */
    public function internalerror() {
        return $this->internalerror;
    }

    /**
     * @return string
     */
    public function last_request_xml() {
        return $this->beautify($this->__getLastRequest());
    }

    /**
     * @return string
     */
    public function last_response_xml() {
        return $this->beautify($this->__getLastResponse());
    }
}