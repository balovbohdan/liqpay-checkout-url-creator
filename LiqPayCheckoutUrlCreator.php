<?
class LiqPayCheckoutUrlCreator {
    /**
     * @param array $apiParams
     *     $apiParams = [
     *         public_key => string,
 *             private_key => string
     *     ]
     */
    static function create(array $apiParams, array $paymentParams) {
        $self = new self($apiParams, $paymentParams);

        return $self->doCreate();
    }

    /**
     * @param array $apiParams
     *     $apiParams = [
     *         public_key => string,
     *         private_key => string
     *     ]
     */
    private function __construct(array $apiParams, array $paymentParams) {
        $this->apiParams = $apiParams;

        $this->paymentParams = self::preparePaymentParams(
            $apiParams,
            $paymentParams
        );
    }

    private function doCreate() {
        $response = $this->makeApiRequest();

        return self::parseApiResponse($response);
    }

    private function makeApiRequest() {
        $body = $this->createApiRequestBody();

        return self::doMakeApiRequest($body);
    }

    private function createApiRequestBody() {
        $data = $this->createData();
        $signature = $this->createSignature($data);

        $body = [
            'data' => $data,
            'signature' => $signature
        ];

        return http_build_query($body);
    }

    private function createData() {
        $paramsStr = json_encode($this->paymentParams);

        return base64_encode($paramsStr);
    }

    /**
     * @param string $data
     */
    private function createSignature($data) {
        $privateKey = $this->apiParams['private_key'];

        $raw = $privateKey . $data . $privateKey;

        return base64_encode(sha1($raw, 1));
    }

    /**
     * @param string $response
     */
    private static function parseApiResponse($response) {
        preg_match(
            '/Location: (https:.+)\b/',
            $response,
            $matches
        );

        return $matches[1];
    }

    /**
     * @param string $body
     * @return string
     */
    private static function doMakeApiRequest($body) {
        $url = 'https://www.liqpay.ua/api/3/checkout';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    private static function preparePaymentParams(array $apiParams, array $paymentParams) {
        $shred = [
            'public_key' => $apiParams['public_key']
        ];

        return array_merge($shred, $paymentParams);
    }

    private $apiParams = [];
    private $paymentParams = [];
}
