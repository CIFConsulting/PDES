<?php

class PDESValidator
{
    protected $schemaPath = __DIR__ . '/xsd/SELISPDES_2p0.xsd';
    protected $errors;

    /**
     * XmlValidator constructor.
     */
    public function __construct()
    {
        $this->reader = new \XMLReader();
    }

    /**
     * @return array
     */
    private function libxmlErrors()
    {
        $errors = libxml_get_errors();
        $result = [];
        foreach ($errors as $error) {
            $result[] = array(
                'code' => $error->code,
                'file' => $error->file,
                'line' => $error->line,
                'message' => trim($error->message)
            );
        }
        libxml_clear_errors();
        return $result;
    }

    /**
     * @param $xml
     * @return bool
     * @throws Exception
     */
    public function validate($xml)
    {
        if (!file_exists($this->schemaPath)) {
            throw new \InvalidArgumentException('SELIS PDES XML Schema could not found');
        }

        $this->reader->open($xml);
        $this->reader->setSchema($this->schemaPath);

        libxml_use_internal_errors(true);

        while ($this->reader->read()) {
            if (!$this->reader->isValid()) {
                $this->errors = $this->libxmlErrors();
            } else {
                return true;
            }
        };
    }

    /**
     * Display Error if Resource is not validated
     *
     * @return array
     */
    public function displayErrors()
    {
        echo '<pre>'; print_r($this->errors); echo '</pre>';
    }
}


$pdes = new \PDESValidator;
$validated = $pdes->validate('pdes-20191024-1.xml');

if ($validated) {
    echo "SELIS PDES XML successfully validated";
} else {
    print_r($pdes->displayErrors());
}
