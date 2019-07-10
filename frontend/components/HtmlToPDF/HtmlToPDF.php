<?php


/**
 * HtmlToPDF Class that uses 'wkhtmltopdf' binary to transform an HTML url into a PDF.
 *
 * @Binary by wkhtmltopdf
 * @link: https://github.com/wkhtmltopdf/wkhtmltopdf
 *
 */

namespace frontend\components\HtmlToPDF;

use Yii;
use yii\base\{
    Exception, Response
};


/**
 * Class HtmlToPDF
 *
 * @package backend\components\HtmlToPDF
 */

class HtmlToPDF {

    public $targetDir = 'temp/';

    const BINARY = 'bin/wkhtmltopdf';

    /**
     * wkhtmltopdf constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = []) {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

    private function adjustExecutionType() {
        $rootDirectory = $this->getRootDirectory();

        shell_exec("chmod +x {$rootDirectory}/bin/wkhtmltopdf");

        /*$result = shell_exec("[ -f {$rootDirectory}/bin/wkhtmltopdf ] && echo \"Found\" || echo \"Not found\"");

        echo "<pre>";
        print_r($result);
        echo "</pre>";


        $result = shell_exec("[ -x {$rootDirectory}/bin/wkhtmltopdf ] && echo \"Executable\" || echo \"Not executable\"");

        echo "<pre>";
        print_r($result);
        echo "</pre>";

        $result = shell_exec("cd .. && cd backend/components/HtmlToPDF/bin/ && ls -l");

        echo "<pre>";
        print_r($result);
        echo "</pre>";

        $result = shell_exec('whoami');

        echo "<pre>";
        print_r($result);
        echo "</pre>";



        $result = shell_exec("sudo {$rootDirectory}/bin/wkhtmltopdf -H");

        echo "command: {$rootDirectory}/bin/wkhtmltopdf -H";

        echo "<pre>";
        var_dump($result);
        echo "</pre>";

        exit;*/
    }

    /**
     * @param string $url
     * @param array $options
     * @param bool $attachment
     * @param bool $forceReload
     *
     * @return \yii\base\Response
     */
    public function convertHtmlToPDF(string $url, array $options = [], bool $attachment = false, bool $forceReload = true) {
        $outputFile = $this->createPdf($url, $options, $forceReload);

        $fileName = basename($outputFile);

        if (!$attachment) {
            Yii::$app->response->headers->set('Content-Disposition', "filename=\"{$fileName}\"");
        }

        return Yii::$app->response->sendFile($outputFile, $fileName);
    }

    /**
     * @param string $url
     *
     * @param array $options
     * @param bool $forceReload
     *
     * @return null|string
     * @throws \yii\base\Exception
     */
    private function createPdf(string $url, array $options, bool $forceReload = false) {
        $rootDirectory = $this->getRootDirectory();

        $directory = file_exists($this->targetDir) ? $this->targetDir : "{$rootDirectory}/{$this->targetDir}";
        $outputFile = $directory . md5($url) . '.pdf';

        $processUrl = true;

        if (file_exists($outputFile) && !$forceReload) {
            $processUrl = false;
        }

        if ($processUrl) {
            try {
                $documentOptions = '';

                if (!empty($options)) {
                    foreach ($options as $option => $value) {
                        $documentOptions .= "{$option} {$value} ";
                    }
                }

                // Live
                //shell_exec("/usr/local/bin/wkhtmltopdf {$documentOptions}'{$url}' {$outputFile}");

                // Local
                shell_exec("{$rootDirectory}/bin/wkhtmltopdf {$documentOptions}'{$url}' {$outputFile}");

                /*$result = shell_exec("wkhtmltopdf {$documentOptions}'{$url}' {$outputFile}");

                if (is_null($result)) {
                    $this->adjustExecutionType();

                    shell_exec("wkhtmltopdf {$documentOptions}'{$url}' {$outputFile}");
                } else {
                    echo "<pre>";
                    var_dump($result);
                    echo "</pre>";

                    exit;
                }*/

                if (!file_exists($outputFile)) {
                    throw new Exception("{$outputFile} doesn't exist.");
                }

                if (!is_readable($outputFile)) {
                    throw new Exception("{$outputFile} is unreadable.");
                }
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        return $outputFile;
    }

    /**
     * @return string
     */
    private function getRootDirectory() {
        return Yii::$app->basePath . '/components/HtmlToPDF';
    }
}
