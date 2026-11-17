<?php

/** Download listing and file allowlist integration. Maintainer: B. */

declare(strict_types=1);

namespace frontend\tests\Functional;

use common\models\AssignmentDownload;
use frontend\tests\Support\FunctionalTester;
use Yii;

final class DownloadCest
{
    private array $_files = [];

    public function _before(): void
    {
        foreach (array_keys(AssignmentDownload::groups()) as $group) {
            $path = dirname(__DIR__, 4) . '/data/' . $group . '/download-test-' . bin2hex(random_bytes(5)) . '.zip';
            file_put_contents($path, "PK\x05\x06" . str_repeat("\0", 18));
            $this->_files[$group] = $path;
        }
    }

    public function _after(): void
    {
        foreach ($this->_files as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        $this->_files = [];
    }

    public function bothCourseworkGroupsAreListed(FunctionalTester $I): void
    {
        $I->amOnRoute('download/index');
        $I->see('团队作业', 'h2');
        $I->see('个人作业', 'h2');
        foreach ($this->_files as $path) {
            $I->see(basename($path), 'a');
        }
    }

    public function listedZipIsReturnedAsAttachment(FunctionalTester $I): void
    {
        foreach ($this->_files as $group => $path) {
            $id = hash('sha256', $group . ':' . basename($path));
            $I->amOnRoute('download/file', ['group' => $group, 'id' => $id]);
            $I->seeResponseCodeIs(200);
            $I->assertSame('application/zip', Yii::$app->response->headers->get('Content-Type'));
            $I->assertStringContainsString('attachment;', Yii::$app->response->headers->get('Content-Disposition'));
        }
    }

    public function unknownIdsAndPathsCannotBeDownloaded(FunctionalTester $I): void
    {
        foreach (
            [['team', str_repeat('0', 64)], ['../', str_repeat('0', 64)],
            ['personal', '../../advanced/common/config/main-local.php']] as [$group, $id]
        ) {
            $I->amOnRoute('download/file', ['group' => $group, 'id' => $id]);
            $I->seeResponseCodeIs(404);
        }
        $I->assertNull(AssignmentDownload::find('team', hash('sha256', 'team:' . basename($this->_files['personal']))));
    }
}
