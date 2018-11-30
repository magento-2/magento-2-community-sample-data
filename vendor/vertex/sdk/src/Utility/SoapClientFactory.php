<?php

/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype Development         <diveinto@mediotype.com>
 */

namespace Vertex\Utility;

/**
 * Create an instance of SoapClient
 *
 * @api
 */
class SoapClientFactory
{
    /**
     * Collection of alternative ciphers for legacy PHP TLS negotiation.
     *
     * @var string[]
     */
    private $legacyCiphers = [
        'DHE-RSA-AES256-SHA',
        'DHE-DSS-AES256-SHA',
        'AES256-SHA',
        'KRB5-DES-CBC3-MD5',
        'KRB5-DES-CBC3-SHA',
        'EDH-RSA-DES-CBC3-SHA',
        'EDH-DSS-DES-CBC3-SHA',
        'DES-CBC3-SHA',
        'DES-CBC3-MD5',
        'DHE-RSA-AES128-SHA',
        'DHE-DSS-AES128-SHA',
        'AES128-SHA',
        'RC2-CBC-MD5',
        'KRB5-RC4-MD5',
        'KRB5-RC4-SHA',
        'RC4-SHA',
        'RC4-MD5',
        'RC4-MD5',
        'KRB5-DES-CBC-MD5',
        'KRB5-DES-CBC-SHA',
        'EDH-RSA-DES-CBC-SHA',
        'EDH-DSS-DES-CBC-SHA',
        'DES-CBC-SHA',
        'DES-CBC-MD5',
        'EXP-KRB5-RC2-CBC-MD5',
        'EXP-KRB5-DES-CBC-MD5',
        'EXP-KRB5-RC2-CBC-SHA',
        'EXP-KRB5-DES-CBC-SHA',
        'EXP-EDH-RSA-DES-CBC-SHA',
        'EXP-EDH-DSS-DES-CBC-SHA',
        'EXP-DES-CBC-SHA',
        'EXP-RC2-CBC-MD5',
        'EXP-RC2-CBC-MD5',
        'EXP-KRB5-RC4-MD5',
        'EXP-KRB5-RC4-SHA',
        'EXP-RC4-MD5',
        'EXP-RC4-MD5',
    ];

    /**
     * Create an instance of SoapClient
     *
     * @param string $wsdl
     * @param array $options
     * @return \SoapClient
     */
    public function create($wsdl, array $options = [])
    {
        $options = array_merge($this->getDefaultOptions(), $options ?: []);
        return new \SoapClient($wsdl, $options);
    }

    /**
     * Get Default SOAP options
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return [
            'trace' => true,
            'soap_version' => SOAP_1_1,
            'stream_context' => [
                'ssl_method' => SOAP_SSL_METHOD_TLS,
                'stream_context' => $this->getStreamContext(),
            ]
        ];
    }

    /**
     * Generate a stream context for the client.
     *
     * @return resource
     */
    private function getStreamContext()
    {
        if (version_compare(PHP_VERSION, '5.6') < 0) {
            return stream_context_create(
                [
                    'ssl' => [
                        'ciphers' => implode(':', $this->legacyCiphers),
                    ],
                ]
            );
        }

        return stream_context_create(
            [
                'ssl' => [
                    'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
                    'ciphers' => 'SHA2',
                ],
            ]
        );
    }
}
