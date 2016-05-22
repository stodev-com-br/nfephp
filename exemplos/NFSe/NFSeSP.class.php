<?php

/**
 * Creates XMLs and Webservices communication
 *
 * Original names of Brazil specific abbreviations have been kept:
 * - CNPJ = Federal Tax Number
 * - CPF = Personal/Individual Taxpayer Registration Number
 * - CCM = Taxpayer Register (for service providers who pay ISS for local town/city hall)
 * - ISS = Service Tax
 *
 * @package   NFePHPaulista
 * @author    Reinaldo Nolasco Sanches <reinaldo@mandic.com.br>
 * @copyright Copyright (c) 2010, Reinaldo Nolasco Sanches
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
<<<<<<< HEAD

class NFSeSP
{
    private $cnpjPrestador = 'xxxxxxxxxxxxx'; // Your CNPJ
    private $ccmPrestador = 'xxxxxxxxx'; // Your CCM
    private $passphrase = 'xxxxxxxxxx'; // Cert passphrase
    private $pkcs12  = 'caminho_completo_para_o_seu_certificado.pfx';
    private $certDir = 'diretorio_onde_esta_seu_certificado'; // Dir for .pem certs
    private $privateKey;
    public $certDaysToExpire=0;
    private $publicKey;
    private $X509Certificate;
    private $key;
=======
class NFSeSP
{
    /**
     * Provisional Receipt of Services
     */
    const TYPE_RPS = 'RPS';
    /**
     * Provisional Receipt of Services from Combined Invoice (mixed)
     */
    const TYPE_RPSM = 'RPS-M';
    /**
     * Coupon
     */
    const TYPE_RPSC = 'RPS-C';
    /**
     * Document Ok
     */
    const STATUS_NORMAL = 'N';
    /**
     * Document is canceled
     */
    const STATUS_CANCELED = 'C';
    /**
     * Document lost
     */
    const STATUS_LOST = 'E';
    /**
     * Taxation in Sao Paulo
     */
    const TAXATION_IN_SAO_PAULO = 'T';
    /**
     * Taxation out of Sao Paulo
     */
    const TAXATION_OUT_SAO_PAULO = 'F';
    /**
     * Taxation free
     */
    const TAXATION_FREE = 'I';
    /**
     * Service Tax Suspended for Judicial Decision
     */
    const TAXATION_SERVICE_TAX_SUSPENDED = 'J';

    private $cnpjPrestador = 'xxxxxxxxxxxxx'; // Your CNPJ
    private $ccmPrestador = 'xxxxxxxxx'; // Your CCM
    private $passphrase = 'xxxxxxxxxx'; // Cert passphrase
    private $pkcs12 = 'caminho_completo_para_o_seu_certificado.pfx';
    private $certDir = 'diretorio_onde_esta_seu_certificado'; // Dir for .pem certs
    private $rpsDirectory = '/patch/for/rps/batch/file';
    private $privateKey = 'privatekey.pem';
    public $certDaysToExpire = 0;
    private $ignoreCertExpired = false;
    private $publicKey = 'publickey.pem';
    private $X509Certificate;
    private $key = 'key.pem';
>>>>>>> upstream/master
    private $connectionSoap;
    private $urlXsi = 'http://www.w3.org/2001/XMLSchema-instance';
    private $urlXsd = 'http://www.w3.org/2001/XMLSchema';
    private $urlNfe = 'http://www.prefeitura.sp.gov.br/nfe';
    private $urlDsig = 'http://www.w3.org/2000/09/xmldsig#';
    private $urlCanonMeth = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
    private $urlSigMeth = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
    private $urlTransfMeth_1 = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';
    private $urlTransfMeth_2 = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
    private $urlDigestMeth = 'http://www.w3.org/2000/09/xmldsig#sha1';


<<<<<<< HEAD
    public function __construct()
    {
        $this->privateKey = $this->certDir . '/privatekey.pem';
        $this->publicKey = $this->certDir . '/publickey.pem';
        $this->key = $this->certDir . '/key.pem';
        if ($this->loadCert()) {
            error_log(__METHOD__ . ': Certificado OK!');
        } else {
            error_log(__METHOD__ . ': Certificado não OK!');
        }
    }

=======
    public function __construct(array $config = array())
    {
        $this->loadConfiguration($config);
        $this->privateKey = $this->certDir . DIRECTORY_SEPARATOR . $this->privateKey;
        $this->publicKey = $this->certDir . DIRECTORY_SEPARATOR . $this->publicKey;
        $this->key = $this->certDir . DIRECTORY_SEPARATOR . $this->key;
        $this->loadCert();
    }

    /**
     * Load given configuration
     *
     * @param array $config
     * @return void
     */
    private function loadConfiguration(array $config)
    {
        foreach ($config as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Validate if certificate is expired.
     *
     * @param string $cert
     * @return void
     */
>>>>>>> upstream/master
    private function validateCert($cert)
    {
        $data = openssl_x509_read($cert);
        $certData = openssl_x509_parse($data);

<<<<<<< HEAD
        $certValidDate = gmmktime(0, 0, 0, substr($certData['validTo'], 2, 2), substr($certData['validTo'], 4, 2), substr($certData['validTo'], 0, 2));
        // obtem o timestamp da data de hoje
        $dHoje = gmmktime(0, 0, 0, date("m"), date("d"), date("Y"));
        if ($certValidDate < time()) {
            error_log(__METHOD__ . ': Certificado expirado em ' . date('Y-m-d', $certValidDate));
            return false;
        }
        //diferença em segundos entre os timestamp
        $diferenca = $certValidDate - $dHoje;
        // convertendo para dias
        $diferenca = round($diferenca /(60*60*24), 0);
        //carregando a propriedade
        $this->certDaysToExpire = $diferenca;
        return true;
    }

    private function loadCert()
    {
        $x509CertData = array();
        if (! openssl_pkcs12_read(file_get_contents($this->pkcs12), $x509CertData, $this->passphrase)) {
            error_log(__METHOD__ . ': Certificado não pode ser lido. O arquivo esta corrompido ou em formato invalido.');
            return false;
        }
        $this->X509Certificate = preg_replace("/[\n]/", '', preg_replace('/\-\-\-\-\-[A-Z]+ CERTIFICATE\-\-\-\-\-/', '', $x509CertData['cert']));
        if (! self::validateCert($x509CertData['cert'])) {
            return false;
        }
        if (! is_dir($this->certDir)) {
            if (! mkdir($this->certDir, 0777)) {
                error_log(__METHOD__ . ': Falha ao criar o diretorio ' . $this->certDir);
                return false;
            }
        }
        if (! file_exists($this->privateKey)) {
            if (! file_put_contents($this->privateKey, $x509CertData['pkey'])) {
                error_log(__METHOD__ . ': Falha ao criar o arquivo ' . $this->privateKey);
                return false;
            }
        }
        if (! file_exists($this->publicKey)) {
            if (! file_put_contents($this->publicKey, $x509CertData['cert'])) {
                error_log(__METHOD__ . ': Falha ao criar o arquivo ' . $this->publicKey);
                return false;
            }
        }
        if (! file_exists($this->key)) {
            if (! file_put_contents($this->key, $x509CertData['cert'] . $x509CertData['pkey'])) {
                error_log(__METHOD__ . ': Falha ao criar o arquivo ' . $this->key);
                return false;
            }
        }
        return true;
    }

=======
        $validTo = \DateTime::createFromFormat('ymd', substr($certData['validTo'], 0, 6));
        $today = new \DateTime('now');
        if (!$this->ignoreCertExpired AND $validTo < $today) {
            throw new \Common\Exception\RuntimeException('Certificado expirado em ' . $validTo->format('Y-m-d'));
        }
    }

    /**
     * Load certificate from file.
     *
     * @return void
     */
    private function loadCert()
    {
        $x509CertData = array();
        if (!openssl_pkcs12_read(file_get_contents($this->pkcs12), $x509CertData, $this->passphrase)) {
            throw new \Common\Exception\IOException(
                'Certificado não pode ser lido. O arquivo esta corrompido ou em formato invalido.'
            );
        }
        $this->X509Certificate = preg_replace("/[\n]/", '', preg_replace('/\-\-\-\-\-[A-Z]+ CERTIFICATE\-\-\-\-\-/', '', $x509CertData['cert']));
        $this->validateCert($x509CertData['cert']);
    }

    /**
     * Start a connection with webservice.
     *
     * @return void
     */
>>>>>>> upstream/master
    public function start()
    {
        //versão do SOAP
        $soapver = SOAP_1_2;
        $wsdl = 'https://nfe.prefeitura.sp.gov.br/ws/lotenfe.asmx?WSDL';
        $params = array(
            'local_cert' => $this->key,
            'passphrase' => $this->passphrase,
            'connection_timeout' => 300,
            'encoding' => 'UTF-8',
<<<<<<< HEAD
            'verifypeer'    => false,
            'verifyhost'    => false,
            'soap_version'  => $soapver,
            'trace'         => true,
=======
            'verifypeer' => false,
            'verifyhost' => false,
            'soap_version' => $soapver,
            'trace' => true,
>>>>>>> upstream/master
            'cache_wsdl' => WSDL_CACHE_NONE
        );

        try {
            $this->connectionSoap = new SoapClient($wsdl, $params);
        } catch (SoapFault $e) {
<<<<<<< HEAD
            error_log('Exception: ' . $e->getMessage());
            echo "erro de conexão soap. Tente novamente mais tarde !<br>\n";
            echo $e->getMessage();
            return false;
        }
    }

    private function send($operation, $xmlDoc)
    {
        self::start();
        $this->signXML($xmlDoc);
=======
            throw new \Common\Exception\RuntimeException($e->getMessage());
        } catch (Exception $e) {
            throw new $e;
        }
    }

    /**
     * Call method from webservice.
     *
     * @param string $operation Method's name to call.
     * @param DOMDocument $xmlDoc Message to be sent.
     * @return bool|SimpleXMLElement Returns a XML when communication is successful, otherwise false when get error.
     */
    private function send($operation, DOMDocument $xmlDoc)
    {
        $this->start();
        $this->signXML($xmlDoc);
        $xmlDoc->formatOutput = true;
>>>>>>> upstream/master
        $params = array(
            'VersaoSchema' => 1,
            'MensagemXML' => $xmlDoc->saveXML()
        );
        try {
            $result = $this->connectionSoap->$operation($params);
        } catch (SoapFault $e) {
<<<<<<< HEAD
            error_log('Exception: ' . $e->getMessage());
            echo "erro soap ".$e->getMessage();
            return false;
=======
            throw new \Common\Exception\RuntimeException($e->getMessage());
        } catch (Exception $e) {
            throw new $e;
>>>>>>> upstream/master
        }
        return new SimpleXMLElement($result->RetornoXML);
    }

<<<<<<< HEAD
    private function createXML($operation)
    {
        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->preservWhiteSpace = false;
        $xmlDoc->formatOutput = false;
        $data = '<?xml version="1.0" encoding="UTF-8"?><Pedido' . $operation . ' xmlns:xsd="' . $this->urlXsd .'" xmlns="' . $this->urlNfe . '" xmlns:xsi="' . $this->urlXsi . '"></Pedido' . $operation . '>';
=======
    /**
     * Create a XML Header Message
     *
     * @param string $operation Method's name
     * @return DOMDocument Returns a XML based on $operation.xsd schema
     */
    private function makeXmlHeader($operation)
    {
        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->preserveWhiteSpace = false;
        $xmlDoc->formatOutput = false;
        $data = '<?xml version="1.0" encoding="UTF-8"?><Pedido' . $operation . ' xmlns:xsd="' . $this->urlXsd . '" xmlns="' . $this->urlNfe . '" xmlns:xsi="' . $this->urlXsi . '"></Pedido' . $operation . '>';
>>>>>>> upstream/master
        $xmlDoc->loadXML(str_replace(array("\r\n", "\n", "\r"), '', $data), LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $root = $xmlDoc->documentElement;
        $header = $xmlDoc->createElementNS('', 'Cabecalho');
        $root->appendChild($header);
        $header->setAttribute('Versao', 1);
        $cnpjSender = $xmlDoc->createElement('CPFCNPJRemetente');
        $cnpjSender->appendChild($xmlDoc->createElement('CNPJ', $this->cnpjPrestador));
        $header->appendChild($cnpjSender);
        return $xmlDoc;
    }

<<<<<<< HEAD
    private function createXMLp1($operation)
    {
        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->preservWhiteSpace = false;
        $xmlDoc->formatOutput = false;
        $data = '<?xml version="1.0" encoding="UTF-8"?><Pedido'.$operation.' xmlns="' . $this->urlNfe . '" xmlns:xsi="' . $this->urlXsi . '"></Pedido' . $operation . '>';
=======
    /**
     * Create a XML Header Message
     *
     * @param string $operation Method's name
     * @return DOMDocument Returns a XML based on $operation.xsd schema
     */
    private function createXMLp1($operation)
    {
        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->preserveWhiteSpace = false;
        $xmlDoc->formatOutput = false;
        $data = '<?xml version="1.0" encoding="UTF-8"?><Pedido' . $operation . ' xmlns="' . $this->urlNfe . '" xmlns:xsi="' . $this->urlXsi . '"></Pedido' . $operation . '>';
>>>>>>> upstream/master
        $xmlDoc->loadXML(str_replace(array("\r\n", "\n", "\r"), '', $data), LIBXML_NOBLANKS | LIBXML_NOEMPTYTAG);
        $root = $xmlDoc->documentElement;
        $header = $xmlDoc->createElementNS('', 'Cabecalho');
        $root->appendChild($header);
        $header->setAttribute('Versao', 1);
        $cnpjSender = $xmlDoc->createElement('CPFCNPJRemetente');
        $cnpjSender->appendChild($xmlDoc->createElement('CNPJ', $this->cnpjPrestador));
        $header->appendChild($cnpjSender);
        return $xmlDoc;
    }

<<<<<<< HEAD
    private function signXML(&$xmlDoc)
=======
    /**
     * Sign XML with certificate.
     * @param DOMDocument $xmlDoc Returns Signature node based on xmldsig-core-schema.xsd schema
     */
    private function signXML(DOMDocument $xmlDoc)
>>>>>>> upstream/master
    {
        $root = $xmlDoc->documentElement;
        // DigestValue is a base64 sha1 hash with root tag content without Signature tag
        $digestValue = base64_encode(hash('sha1', $root->C14N(false, false, null, null), true));
        $signature = $xmlDoc->createElementNS($this->urlDsig, 'Signature');
        $root->appendChild($signature);
        $signedInfo = $xmlDoc->createElement('SignedInfo');
        $signature->appendChild($signedInfo);
        $newNode = $xmlDoc->createElement('CanonicalizationMethod');
        $signedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->urlCanonMeth);
        $newNode = $xmlDoc->createElement('SignatureMethod');
        $signedInfo->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->urlSigMeth);
        $reference = $xmlDoc->createElement('Reference');
        $signedInfo->appendChild($reference);
        $reference->setAttribute('URI', '');
        $transforms = $xmlDoc->createElement('Transforms');
        $reference->appendChild($transforms);
        $newNode = $xmlDoc->createElement('Transform');
        $transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->urlTransfMeth_1);
        $newNode = $xmlDoc->createElement('Transform');
        $transforms->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->urlTransfMeth_2);
        $newNode = $xmlDoc->createElement('DigestMethod');
        $reference->appendChild($newNode);
        $newNode->setAttribute('Algorithm', $this->urlDigestMeth);
        $newNode = $xmlDoc->createElement('DigestValue', $digestValue);
        $reference->appendChild($newNode);
        // SignedInfo Canonicalization (Canonical XML)
        $signedInfoC14n = $signedInfo->C14N(false, false, null, null);
        // SignatureValue is a base64 SignedInfo tag content
        $signatureValue = '';
        $pkeyId = openssl_get_privatekey(file_get_contents($this->privateKey));
        openssl_sign($signedInfoC14n, $signatureValue, $pkeyId);
        $newNode = $xmlDoc->createElement('SignatureValue', base64_encode($signatureValue));
        $signature->appendChild($newNode);
        $keyInfo = $xmlDoc->createElement('KeyInfo');
        $signature->appendChild($keyInfo);
        $x509Data = $xmlDoc->createElement('X509Data');
        $keyInfo->appendChild($x509Data);
        $newNode = $xmlDoc->createElement('X509Certificate', $this->X509Certificate);
        $x509Data->appendChild($newNode);
        openssl_free_key($pkeyId);
    }

<<<<<<< HEAD
    private function signRPS(NFeRPS $rps, &$rpsNode)
    {
        $content = sprintf('%08s', $rps->CCM).
            sprintf('%-5s', $rps->serie).
            sprintf('%012s', $rps->numero).
            str_replace("-", "", $rps->dataEmissao).
            $rps->tributacao .
            $rps->status .
            (($rps->comISSRetido) ? 'S' : 'N') .
            sprintf('%015s', str_replace(array('.', ','), '', number_format($rps->valorServicos, 2))).
            sprintf('%015s', str_replace(array('.', ','), '', number_format($rps->valorDeducoes, 2))).
=======
    /**
     * Sign XML with certificate.
     * @param NFeRPS $rps RPS Document
     * @param DOMElement $rpsNode Returns Assinatura node based on $operation.xsd schema
     */
    private function signRPS(NFeRPS $rps, DOMElement $rpsNode)
    {
        $content = sprintf('%08s', $rps->CCM) .
            sprintf('%-5s', $rps->serie) .
            sprintf('%012s', $rps->numero) .
            str_replace("-", "", $rps->dataEmissao) .
            $rps->tributacao .
            $rps->status .
            (($rps->comISSRetido) ? 'S' : 'N') .
            sprintf('%015s', str_replace(array('.', ','), '', number_format($rps->valorServicos, 2))) .
            sprintf('%015s', str_replace(array('.', ','), '', number_format($rps->valorDeducoes, 2))) .
>>>>>>> upstream/master
            sprintf('%05s', $rps->codigoServico) .
            (($rps->contractorRPS->type == 'F') ? '1' : '2') .
            sprintf('%014s', $rps->contractorRPS->cnpjTomador);

        $signatureValue = '';
        $pkeyId = openssl_get_privatekey(file_get_contents($this->privateKey));
        openssl_sign($content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyId);
        $rpsNode->appendChild(new DOMElement('Assinatura', base64_encode($signatureValue)));
    }

<<<<<<< HEAD
    private function insertRPS(NFeRPS $rps, &$xmlDoc)
=======
    /**
     * Makes a XML Object based on given RPS.
     * @param NFeRPS $rps RPS Document
     * @param DOMDocument $xmlDoc Returns xml based on RetornoEnvioRPS.xsd schema
     */
    private function makeRPSXml(NFeRPS $rps, DOMDocument $xmlDoc)
>>>>>>> upstream/master
    {
        $rpsNode = $xmlDoc->createElementNS('', 'RPS');
        $xmlDoc->documentElement->appendChild($rpsNode);
        $this->signRPS($rps, $rpsNode);
        $rpsKey = $xmlDoc->createElement('ChaveRPS'); // 1-1
        $rpsKey->appendChild($xmlDoc->createElement('InscricaoPrestador', $rps->CCM)); // 1-1
        $rpsKey->appendChild($xmlDoc->createElement('SerieRPS', $rps->serie)); // 1-1 DHC AAAAA / alog AAAAB
        $rpsKey->appendChild($xmlDoc->createElement('NumeroRPS', $rps->numero)); // 1-1
        $rpsNode->appendChild($rpsKey);
        /* RPS ­ Recibo Provisório de Serviços
         * RPS-M ­ Recibo Provisório de Serviços proveniente de Nota Fiscal Conjugada (Mista)
        * RPS-C ­ Cupom */
        $rpsNode->appendChild($xmlDoc->createElement('TipoRPS', $rps->type)); // 1-1
        $rpsNode->appendChild($xmlDoc->createElement('DataEmissao', $rps->dataEmissao)); // 1-1
        /* N ­ Normal
        * C ­ Cancelada
        * E ­ Extraviada */
        $rpsNode->appendChild($xmlDoc->createElement('StatusRPS', $rps->status)); // 1-1
        /* T - Tributação no município de São Paulo
         * F - Tributação fora do município de São Paulo
         * I ­- Isento
         * J - ISS Suspenso por Decisão Judicial */
        $rpsNode->appendChild($xmlDoc->createElement('TributacaoRPS', $rps->tributacao)); // 1-1
        $rpsNode->appendChild($xmlDoc->createElement('ValorServicos', sprintf("%s", $rps->valorServicos))); // 1-1
        $rpsNode->appendChild($xmlDoc->createElement('ValorDeducoes', sprintf("%s", $rps->valorDeducoes))); // 1-1
        $rpsNode->appendChild($xmlDoc->createElement('CodigoServico', $rps->codigoServico)); // 1-1
        $rpsNode->appendChild($xmlDoc->createElement('AliquotaServicos', $rps->aliquotaServicos)); // 1-1
        $rpsNode->appendChild($xmlDoc->createElement('ISSRetido', (($rps->comISSRetido) ? 'true' : 'false'))); // 1-1
        $cnpj = $xmlDoc->createElement('CPFCNPJTomador'); // 0-1
        if ($rps->contractorRPS->type == "F") {
            $cnpj->appendChild($xmlDoc->createElement('CPF', sprintf('%011s', $rps->contractorRPS->cnpjTomador)));
        } else {
            $cnpj->appendChild($xmlDoc->createElement('CNPJ', sprintf('%014s', $rps->contractorRPS->cnpjTomador)));
        }
        $rpsNode->appendChild($cnpj);
        if ($rps->contractorRPS->ccmTomador <> "") {
<<<<<<< HEAD
           $rpsNode->appendChild($xmlDoc->createElement('InscricaoMunicipalTomador', $rps->contractorRPS->ccmTomador)); // 0-1
=======
            $rpsNode->appendChild($xmlDoc->createElement('InscricaoMunicipalTomador', $rps->contractorRPS->ccmTomador)); // 0-1
>>>>>>> upstream/master
        }
        $rpsNode->appendChild($xmlDoc->createElement('RazaoSocialTomador', $rps->contractorRPS->name)); // 0-1
        $address = $xmlDoc->createElement('EnderecoTomador'); // 0-1
        $address->appendChild($xmlDoc->createElement('TipoLogradouro', $rps->contractorRPS->tipoEndereco));
        $address->appendChild($xmlDoc->createElement('Logradouro', $rps->contractorRPS->endereco));
        $address->appendChild($xmlDoc->createElement('NumeroEndereco', $rps->contractorRPS->enderecoNumero));
        if (trim($rps->contractorRPS->complemento) != "") {
            $address->appendChild($xmlDoc->createElement('ComplementoEndereco', $rps->contractorRPS->complemento));
        }
        $address->appendChild($xmlDoc->createElement('Bairro', $rps->contractorRPS->bairro));
        $address->appendChild($xmlDoc->createElement('Cidade', $rps->contractorRPS->cidade));
        $address->appendChild($xmlDoc->createElement('UF', $rps->contractorRPS->estado));
        $address->appendChild($xmlDoc->createElement('CEP', $rps->contractorRPS->cep));
        $rpsNode->appendChild($address);
        $rpsNode->appendChild($xmlDoc->createElement('EmailTomador', $rps->contractorRPS->email)); // 0-1
        $rpsNode->appendChild($xmlDoc->createElement('Discriminacao', $rps->discriminacao)); // 1-1
    }

<<<<<<< HEAD
   /**
    * Send a RPS to replace for NF-e
    *
    * @param NFeRPS $rps
    */
    public function sendRPS(NFeRPS $rps)
    {
        $operation = 'EnvioRPS';
        $xmlDoc = $this->createXML($operation);
        $this->insertRPS($rps, $xmlDoc);
=======
    /**
     * Send a RPS to replace for NF-e
     * Message is based on PedidoEnvioRPS.xsd schema
     *
     * @param NFeRPS $rps
     * @return bool|\SimpleXMLElement Returns xml based on RetornoEnvioRPS.xsd schema
     */
    public function sendRPS(NFeRPS $rps)
    {
        $operation = 'EnvioRPS';
        $xmlDoc = $this->makeXmlHeader($operation);
        $this->makeRPSXml($rps, $xmlDoc);
>>>>>>> upstream/master
        $returnXmlDoc = $this->send($operation, $xmlDoc);
        return $returnXmlDoc;
    }

<<<<<<< HEAD


    /**
     * Send a batch of RPSs to replace for NF-e
=======
    /**
     * Send a batch of RPSs to replace for NF-e
     * Message is based on PedidoEnvioLoteRPS.xsd schema
>>>>>>> upstream/master
     *
     * @param array $rangeDate ('start' => start date of RPSs, 'end' => end date of RPSs)
     * @param array $valorTotal ('servicos' => total value of RPSs, 'deducoes' => total deductions on values of RPSs)
     * @param array $rps Collection of NFeRPS
<<<<<<< HEAD
     */
    public function sendRPSBatch($rangeDate, $valorTotal, $rps)
    {
        $operation = 'EnvioLoteRPS';
        $xmlDoc = $this->createXML($operation);
        $header = $xmlDoc->documentElement->getElementsByTagName('Cabecalho')->item(0);
        $header->appendChild($xmlDoc->createElement('transacao', 'false'));
        $header->appendChild($xmlDoc->createElement('dtInicio', $rangeDate['inicio']));
        $header->appendChild($xmlDoc->createElement('dtFim', $rangeDate['fim']));
        $header->appendChild($xmlDoc->createElement('QtdRPS', count($rps)));
        $header->appendChild($xmlDoc->createElement('ValorTotalServicos', $valorTotal['servicos']));
        $header->appendChild($xmlDoc->createElement('ValorTotalDeducoes', $valorTotal['deducoes']));
        foreach ($rps as $item) {
            $this->insertRPS($item, $xmlDoc);
        }
        return $this->send($operation, $xmlDoc);
    }

    /**
     * Send a batch of RPSs to replace for NF-e for test only
=======
     * @return bool|\SimpleXMLElement Returns xml based on RetornoEnvioLoteRPS.xsd schema
     */
    public function sendRPSBatch($rangeDate, $valorTotal, $rps)
    {
        $xmlDoc = $this->makeBatchMessage($rangeDate, $valorTotal, $rps);
        return $this->send('EnvioLoteRPS', $xmlDoc);
    }

    /**
     * Send a batch of RPSs to replace for NF-e for test only.
     * Message is based on PedidoEnvioLoteRPS.xsd schema
>>>>>>> upstream/master
     *
     * @param array $rangeDate ('start' => start date of RPSs, 'end' => end date of RPSs)
     * @param array $valorTotal ('servicos' => total value of RPSs, 'deducoes' => total deductions on values of RPSs)
     * @param array $rps Collection of NFeRPS
<<<<<<< HEAD
     */
    public function sendRPSBatchTest($rangeDate, $valorTotal, $rps)
    {
        $operation = 'EnvioLoteRPS';
        $xmlDoc = $this->createXML($operation);
=======
     * @return bool|\SimpleXMLElement Returns xml based on RetornoEnvioLoteRPS.xsd schema
     */
    public function sendRPSBatchTest(array $rangeDate, array $valorTotal, array $rps)
    {
        $xmlDoc = $this->makeBatchMessage($rangeDate, $valorTotal, $rps);
        $return = $this->send('TesteEnvioLoteRPS', $xmlDoc);
        return $return;
    }

    /**
     * Makes xml batch message.
     * @param array $rangeDate
     * @param array $valorTotal
     * @param array $rps
     * @return DOMDocument
     */
    private function makeBatchMessage(array $rangeDate, array $valorTotal, array $rps)
    {
        $xmlDoc = $this->makeXmlHeader('EnvioLoteRPS');
>>>>>>> upstream/master
        $header = $xmlDoc->documentElement->getElementsByTagName('Cabecalho')->item(0);
        $header->appendChild($xmlDoc->createElement('transacao', 'false'));
        $header->appendChild($xmlDoc->createElement('dtInicio', $rangeDate['inicio']));
        $header->appendChild($xmlDoc->createElement('dtFim', $rangeDate['fim']));
        $header->appendChild($xmlDoc->createElement('QtdRPS', count($rps)));
        $header->appendChild($xmlDoc->createElement('ValorTotalServicos', $valorTotal['servicos']));
        $header->appendChild($xmlDoc->createElement('ValorTotalDeducoes', $valorTotal['deducoes']));
        foreach ($rps as $item) {
<<<<<<< HEAD
            $this->insertRPS($item, $xmlDoc);
        }
        //    $docxml = $xmlDoc->saveXML();
        //    echo "xml gerado[<br>\n";
        //    print_r($docxml);
        //    echo "]<br>\n";
        //    exit();
        $return = $this->send('TesteEnvioLoteRPS', $xmlDoc);
        $xmlDoc->formatOutput = true;
        error_log(__METHOD__ . ': ' . $xmlDoc->saveXML());
        return $return;
    }

    /**
     *
     * @param array $nfe Array of NFe numbers
     */
    public function cancelNFe($nfeNumbers)
    {
        $operation = 'CancelamentoNFe';
        $xmlDoc = $this->createXML($operation);
=======
            $this->makeRPSXml($item, $xmlDoc);
        }
        return $xmlDoc;
    }

    /**
     * Has responsible to cancel NFe numbers created from sendRPSBatch method
     * Message is based on PedidoConsultaNFe.xsd schema
     *
     * @param array $nfeNumbers Array of NFe numbers
     * @return bool|\SimpleXMLElement Returns xml based on RetornoCancelamentoNFe.xsd schema
     */
    public function cancelNFe(array $nfeNumbers)
    {
        $operation = 'CancelamentoNFe';
        $xmlDoc = $this->makeXmlHeader($operation);
>>>>>>> upstream/master
        $root = $xmlDoc->documentElement;
        $header = $root->getElementsByTagName('Cabecalho')->item(0);
        $header->appendChild($xmlDoc->createElement('transacao', 'false'));
        foreach ($nfeNumbers as $nfeNumber) {
            $detail = $xmlDoc->createElementNS('', 'Detalhe');
            $root->appendChild($detail);
            $nfeKey = $xmlDoc->createElement('ChaveNFe'); // 1-1
            $nfeKey->appendChild($xmlDoc->createElement('InscricaoPrestador', $this->ccmPrestador)); // 1-1
            $nfeKey->appendChild($xmlDoc->createElement('NumeroNFe', $nfeNumber)); // 1-1
            $detail->appendChild($nfeKey);
            $content = sprintf('%08s', $this->ccmPrestador) .
                sprintf('%012s', $nfeNumber);
            $signatureValue = '';
<<<<<<< HEAD
            $digestValue = base64_encode(hash('sha1', $content, true));
            $pkeyId = openssl_get_privatekey(file_get_contents($this->privateKey));
            //      openssl_sign($digestValue, $signatureValue, $pkeyId);
=======
            $pkeyId = openssl_get_privatekey(file_get_contents($this->privateKey));
>>>>>>> upstream/master
            openssl_sign($content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1);
            openssl_free_key($pkeyId);
            $detail->appendChild(new DOMElement('AssinaturaCancelamento', base64_encode($signatureValue)));
        }
<<<<<<< HEAD
        $docxml = $xmlDoc->saveXML();
        return $this->send($operation, $xmlDoc);
    }

=======
        return $this->send($operation, $xmlDoc);
    }

    /**
     * It will find a NFe document from given number or
     * RPS document when given $rpsNumber and $rpsSerie
     * Message is based on PedidoConsultaNFe.xsd schema
     *
     * @param string $nfeNumber NFe Number
     * @param string $rpsNumber RPS Number
     * @param string $rpsSerie RPS Serie
     * @return bool|SimpleXMLElement Returns a XML based on RetornoConsulta.xsd schema.
     */
>>>>>>> upstream/master
    public function queryNFe($nfeNumber, $rpsNumber, $rpsSerie)
    {
        $operation = 'ConsultaNFe';
        $xmlDoc = $this->createXMLp1($operation);
        $root = $xmlDoc->documentElement;
        if ($nfeNumber > 0) {
            $detailNfe = $xmlDoc->createElementNS('', 'Detalhe');
            $root->appendChild($detailNfe);
            $nfeKey = $xmlDoc->createElement('ChaveNFe'); // 1-1
            $nfeKey->appendChild($xmlDoc->createElement('InscricaoPrestador', $this->ccmPrestador)); // 1-1
            $nfeKey->appendChild($xmlDoc->createElement('NumeroNFe', $nfeNumber)); // 1-1
            $detailNfe->appendChild($nfeKey);
        }
        if ($rpsNumber > 0) {
            //$detailRps = $xmlDoc->createElement('Detalhe');
            $detailRps = $xmlDoc->createElementNS('', 'Detalhe');
            $root->appendChild($detailRps);
            $rpsKey = $xmlDoc->createElement('ChaveRPS'); // 1-1
            $rpsKey->appendChild($xmlDoc->createElement('InscricaoPrestador', $this->ccmPrestador)); // 1-1
            $rpsKey->appendChild($xmlDoc->createElement('SerieRPS', $rpsSerie)); // 1-1 DHC AAAAA / alog AAAAB
            $rpsKey->appendChild($xmlDoc->createElement('NumeroRPS', $rpsNumber)); // 1-1
            $detailRps->appendChild($rpsKey);
        }
        return $this->send($operation, $xmlDoc);
    }

    /**
     * queryNFeReceived and queryNFeIssued have the same XML request model
<<<<<<< HEAD
     *
     * @param string $cnpj
     * @param string $ccm
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     */
    private function queryNFeWithDateRange($cnpj, $ccm, $startDate, $endDate)
    {
        $operation = 'ConsultaNFePeriodo';
        $xmlDoc = $this->createXML($operation);
=======
     * Message is based on PedidoConsultaNFePeriodo.xsd schema
     *
     * @param string $cnpj CNPJ to find
     * @param string $ccm State Registration
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     * @param int $pageNumber Number of page to query results, by default the webservice given 50 documents per page
     * @return \DOMDocument Returns xml based on RetornoConsulta.xsd schema
     */
    private function queryNFeWithDateRange($cnpj, $ccm, $startDate, $endDate, $pageNumber = 1)
    {
        $operation = 'ConsultaNFePeriodo';
        $xmlDoc = $this->makeXmlHeader($operation);
>>>>>>> upstream/master
        $header = $xmlDoc->documentElement->getElementsByTagName('Cabecalho')->item(0);
        $cnpjTaxpayer = $xmlDoc->createElement('CPFCNPJ');
        $cnpjTaxpayer->appendChild($xmlDoc->createElement('CNPJ', $cnpj));
        $header->appendChild($cnpjTaxpayer);
        $ccmTaxpayer = $xmlDoc->createElement('Inscricao', $ccm);
        $header->appendChild($ccmTaxpayer);
        $startDateNode = $xmlDoc->createElement('dtInicio', $startDate);
        $header->appendChild($startDateNode);
        $endDateNode = $xmlDoc->createElement('dtFim', $endDate);
        $header->appendChild($endDateNode);
<<<<<<< HEAD
        $pageNumber = $xmlDoc->createElement('NumeroPagina', 1);
=======
        $pageNumber = $xmlDoc->createElement('NumeroPagina', $pageNumber);
>>>>>>> upstream/master
        $header->appendChild($pageNumber);
        return $xmlDoc;
    }

    /**
     * Query NF-e's that CNPJ/CCM company received from other companies
<<<<<<< HEAD
     *
     * @param string $cnpj
     * @param string $ccm
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     */
    public function queryNFeReceived($cnpj, $ccm, $startDate, $endDate)
    {
        $operation = 'ConsultaNFeRecebidas';
        $xmlDoc = $this->queryNFeWithDateRange($cnpj, $ccm, $startDate, $endDate);
=======
     * Message is based on PedidoConsultaNFePeriodo.xsd schema
     *
     * @param string $cnpj CNPJ to find
     * @param string $ccm State Registration
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     * @param int $pageNumber
     * @return bool|\SimpleXMLElement Returns xml based on RetornoConsulta.xsd schema
     */
    public function queryNFeReceived($cnpj, $ccm, $startDate, $endDate, $pageNumber = 1)
    {
        $operation = 'ConsultaNFeRecebidas';
        $xmlDoc = $this->queryNFeWithDateRange($cnpj, $ccm, $startDate, $endDate, $pageNumber);
>>>>>>> upstream/master
        return $this->send($operation, $xmlDoc);
    }

    /**
     * Query NF-e's that CNPJ/CCM company issued to other companies
<<<<<<< HEAD
=======
     * Message is based on PedidoConsultaNFePeriodo.xsd schema
>>>>>>> upstream/master
     *
     * @param string $cnpj
     * @param string $ccm
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
<<<<<<< HEAD
     */
    public function queryNFeIssued($cnpj, $ccm, $startDate, $endDate)
    {
        $operation = 'ConsultaNFeEmitidas';
        $xmlDoc = $this->queryNFeWithDateRange($cnpj, $ccm, $startDate, $endDate);
        return $this->send($operation, $xmlDoc);
    }

    public function queryBatch($batchNumber)
    {
        $operation = 'ConsultaLote';
        $xmlDoc = $this->createXML($operation);
=======
     * @param int $pageNumber
     * @return bool|\SimpleXMLElement Returns xml based on RetornoConsulta.xsd schema
     */
    public function queryNFeIssued($cnpj, $ccm, $startDate, $endDate, $pageNumber = 1)
    {
        $operation = 'ConsultaNFeEmitidas';
        $xmlDoc = $this->queryNFeWithDateRange($cnpj, $ccm, $startDate, $endDate, $pageNumber);
        return $this->send($operation, $xmlDoc);
    }

    /**
     * Get NF-e's with this batch number
     * Message is based on PedidoConsultaLote.xsd schema
     *
     * @param $batchNumber
     * @return bool|SimpleXMLElement Returns xml based on schema RetornoConsulta.xsd
     */
    public function queryBatch($batchNumber)
    {
        $operation = 'ConsultaLote';
        $xmlDoc = $this->makeXmlHeader($operation);
>>>>>>> upstream/master
        $header = $xmlDoc->documentElement->getElementsByTagName('Cabecalho')->item(0);
        $header->appendChild($xmlDoc->createElement('NumeroLote', $batchNumber));
        return $this->send($operation, $xmlDoc);
    }

<<<<<<< HEAD

    /**
     * If $batchNumber param is null, last match info will be returned
     *
     * @param integer $batchNumber
=======
    /**
     * If $batchNumber param is null, last match info will be returned
     * Message is based on PedidoInformacoesLote.xsd schema
     *
     * @param integer $batchNumber
     * @return bool|SimpleXMLElement Returns xml based on schema RetornoInformacoesLote.xsd
>>>>>>> upstream/master
     */
    public function queryBatchInfo($batchNumber = null)
    {
        $operation = 'InformacoesLote';
<<<<<<< HEAD
        $xmlDoc = $this->createXML($operation);
=======
        $xmlDoc = $this->makeXmlHeader($operation);
>>>>>>> upstream/master
        $header = $xmlDoc->documentElement->getElementsByTagName('Cabecalho')->item(0);
        $header->appendChild($xmlDoc->createElement('InscricaoPrestador', $this->ccmPrestador));
        if ($batchNumber) {
            $header->appendChild($xmlDoc->createElement('NumeroLote', $batchNumber));
        }
        return $this->send($operation, $xmlDoc);
    }

    /**
     * Returns CCM for given CNPJ
<<<<<<< HEAD
     *
     * @param string $cnpj
=======
     * Message is based on PedidoConsultaCNPJ.xsd schema and
     * response is based on RetornoConsultaCNPJ.xsd schema
     *
     * @param string $cnpj
     * @return bool|string Returns the taxpayer register number for given CNPJ
>>>>>>> upstream/master
     */
    public function queryCNPJ($cnpj)
    {
        $operation = 'ConsultaCNPJ';
        $xmlDoc = $this->createXMLp1($operation);
        $root = $xmlDoc->documentElement;
        $cnpjTaxpayer = $xmlDoc->createElementNS('', 'CNPJContribuinte');
        if (strlen($cnpj) == 11) {
<<<<<<< HEAD
            $cnpjTaxpayer->appendChild($xmlDoc->createElement('CPF', (string) sprintf('%011s', $cnpj)));
        } else {
            $cnpjTaxpayer->appendChild($xmlDoc->createElement('CNPJ', (string) sprintf('%014s', $cnpj)));
        }
        $root->appendChild($cnpjTaxpayer);
        $docxml = $xmlDoc->saveXML();
        if ($return = $this->send($operation, $xmlDoc)) {
            if ($return->Detalhe->InscricaoMunicipal <> "") {
                return $return->Detalhe->InscricaoMunicipal;
            } else {
                if ($return->Alerta->Codigo <> "") {
                    return $return->Alerta->Descricao;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
=======
            $cnpjTaxpayer->appendChild($xmlDoc->createElement('CPF', (string)sprintf('%011s', $cnpj)));
        } else {
            $cnpjTaxpayer->appendChild($xmlDoc->createElement('CNPJ', (string)sprintf('%014s', $cnpj)));
        }
        $root->appendChild($cnpjTaxpayer);
        $return = $this->send($operation, $xmlDoc);

        $isSuccess = ($return && (string)$return->Cabecalho->Sucesso == 'true');

        if ($isSuccess && (string)$return->Detalhe->InscricaoMunicipal != "") {
            return (string)$return->Detalhe->InscricaoMunicipal;
        }
        if (!$isSuccess && (string)$return->Alerta->Codigo != "") {
            return (string)$return->Alerta->Descricao;
        }
        return false;
    }

    /**
     * Returns CCM for given CNPJ
     * Message is based on PedidoConsultaCNPJ.xsd schema and
     * response is based on RetornoConsultaCNPJ.xsd schema
     *
     * @param string $cnpj
     * @return bool|string Returns the taxpayer register number for given CNPJ
     */
    public function queryCPF($cnpj)
    {
        $operation = 'ConsultaCNPJ';
        $xmlDoc = $this->createXMLp1($operation);
        $root = $xmlDoc->documentElement;
        $cnpjTaxpayer = $xmlDoc->createElementNS('', 'CNPJContribuinte');
        $cnpjTaxpayer->appendChild($xmlDoc->createElement('CPF', (string)sprintf('%011s', $cnpj)));
        $root->appendChild($cnpjTaxpayer);
        $xmlResponse = $this->send($operation, $xmlDoc);
        if ($xmlResponse) {
            return $this->getIncricaoMunicipal($xmlResponse);
        }
        return false;
    }

    /**
     * Gets Taxpayer register.
     *
     * @param SimpleXMLElement $xmlResponse
     * @return string Returns
     */
    private function getIncricaoMunicipal(SimpleXMLElement $xmlResponse)
    {
        $isSuccess = ($xmlResponse && (string)$xmlResponse->Cabecalho->Sucesso == 'true');

        if (!$isSuccess) {
            throw new \Common\Exception\RuntimeException($xmlResponse->Alerta->Descricao, $xmlResponse->Alerta->Codigo);
        }

        return (string)$xmlResponse->Detalhe->InscricaoMunicipal;
>>>>>>> upstream/master
    }

    /**
     * Create a line with RPS description for batch file
     *
<<<<<<< HEAD
     * @param unknown_type $rps
     * @param unknown_type $body
     */
    private function insertTextRPS(NFeRPS $rps, &$body)
=======
     * @param NFeRPS $rps
     * @param string $body
     * @return string
     */
    private function insertTextRPS(NFeRPS $rps, $body)
>>>>>>> upstream/master
    {
        if ($rps->valorServicos > 0) {
            $line = "2" .
                sprintf("%-5s", $rps->type) .
                sprintf("%-5s", $rps->serie) .
                sprintf('%012s', $rps->numero) .
                str_replace("-", "", $rps->dataEmissao) .
                $rps->tributacao .
                sprintf('%015s', str_replace('.', '', sprintf('%.2f', $rps->valorServicos))) .
                sprintf('%015s', str_replace('.', '', sprintf('%.2f', $rps->valorDeducoes))) .
                sprintf('%05s', $rps->codigoServico) .
                sprintf('%04s', str_replace('.', '', $rps->aliquotaServicos)) .
                (($rps->comISSRetido) ? '1' : '2') .
                (($rps->contractorRPS->type == 'F') ? '1' : '2') .
                sprintf('%014s', $rps->contractorRPS->cnpjTomador) .
                sprintf('%08s', $rps->contractorRPS->ccmTomador) .
                sprintf('%012s', '') .
                sprintf('%-75s', mb_convert_encoding($rps->contractorRPS->name, 'ISO-8859-1', 'UTF-8')) .
                sprintf('%3s', (($rps->contractorRPS->tipoEndereco == 'R') ? 'Rua' : '')) .
                sprintf('%-50s', mb_convert_encoding($rps->contractorRPS->endereco, 'ISO-8859-1', 'UTF-8')) .
                sprintf('%-10s', $rps->contractorRPS->enderecoNumero) .
                sprintf('%-30s', mb_convert_encoding($rps->contractorRPS->complemento, 'ISO-8859-1', 'UTF-8')) .
                sprintf('%-30s', mb_convert_encoding($rps->contractorRPS->bairro, 'ISO-8859-1', 'UTF-8')) .
                sprintf('%-50s', mb_convert_encoding($rps->contractorRPS->cidade, 'ISO-8859-1', 'UTF-8')) .
                sprintf('%-2s', $rps->contractorRPS->estado) .
                sprintf('%08s', $rps->contractorRPS->cep) .
                sprintf('%-75s', $rps->contractorRPS->email) .
                str_replace("\n", '|', mb_convert_encoding($rps->discriminacao, 'ISO-8859-1', 'UTF-8'));
            $body .= $line . chr(13) . chr(10);
        }
<<<<<<< HEAD
=======
        return $body;
>>>>>>> upstream/master
    }

    /**
     * Create a batch file with NF-e text layout
     *
<<<<<<< HEAD
     * @param unknown_type $rangeDate
     * @param unknown_type $valorTotal
     * @param unknown_type $rps
     */
    public function textFile($rangeDate, $valorTotal, $rps)
    {
        $file = '';
=======
     * @param array $rangeDate
     * @param array $valorTotal
     * @param array $rps
     * @return bool|string
     */
    public function textFile($rangeDate, $valorTotal, $rps)
    {
>>>>>>> upstream/master
        $header = "1" .
            "001" .
            $this->ccmPrestador .
            date("Ymd", $rangeDate['inicio']) .
            date("Ymd", $rangeDate['fim']) .
            chr(13) . chr(10);
        $body = '';
        foreach ($rps as $item) {
            $this->insertTextRPS($item, $body);
        }
        $footer = "9" .
            sprintf("%07s", count($rps)) .
            sprintf("%015s", str_replace('.', '', sprintf('%.2f', $valorTotal['servicos']))) .
            sprintf("%015s", str_replace('.', '', sprintf('%.2f', $valorTotal['deducoes']))) .
            chr(13) . chr(10);
<<<<<<< HEAD
        $rpsDir = '/patch/for/rps/batch/file';
        $rpsFileName = date("Y-m-d_Hi") . '.txt';
        $rpsFullPath = $rpsDir . '/' . $rpsFileName;
        if (! is_dir($rpsDir)) {
            if (! mkdir($rpsDir, 0777)) {

            }
        }
        if (! file_put_contents($rpsFullPath, $header . $body . $footer)) {
            error_log(__METHOD__ . ': Cannot create rps file ' . $rpsFullPath);
            return false;
=======
        $rpsFileName = date("Y-m-d_Hi") . '.txt';
        $rpsFullPath = $this->rpsDirectory . DIRECTORY_SEPARATOR . $rpsFileName;
        if (!is_dir($this->rpsDirectory)) {
            mkdir($this->rpsDirectory, 0777, true);
        }
        if (!file_put_contents($rpsFullPath, $header . $body . $footer)) {
            throw new \Common\Exception\IOException(sprintf('Cannot create RPS file (%s)', $rpsFullPath));
>>>>>>> upstream/master
        }
        return $rpsFullPath;
    }
}
