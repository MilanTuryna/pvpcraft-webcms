<?php


namespace App\Model;

use Nette\Caching\Storage;
use Nette\Caching\Cache;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Http\FileUpload;
use Nette\Utils\FileSystem;
use App\Model\API\Status;

/**
 * Class Settings
 * @package App\Model
 */
class SettingsRepository
{
    const PATH = 'img/';

    Private Explorer $db;
    Private Cache $cache;

    /**
     * Settings constructor.
     * @param Explorer $db
     * @param Storage $storage
     */
    public function __construct(Explorer $db, Storage $storage)
    {
        $this->db = $db;
        $this->cache = new Cache($storage);
    }

    /**
     * @param $arr
     * @return int
     */
    public function setRows($arr): ?int {
        return $this->db->table('nastaveni')->where('id', 1)->update($arr);
    }

    /**
     * @return array
     */
    public function getLogo(): array {
        return glob(self::PATH . "logo.*");
    }

    /**
     * @param FileUpload $file
     */
    public function setLogo(FileUpload $file) {
        $suffix = pathinfo($file->getName(), PATHINFO_EXTENSION);
        $path = self::PATH . "logo.{$suffix}";
        $file->move($path);
    }

    public function deleteLogo() {
        $files = glob(self::PATH . "logo.*");
        foreach($files as $file) {
            FileSystem::delete($file);
        }
    }

    /**
     * @return ActiveRow|null
     */
    public function getAllRows(): ?ActiveRow {
        return $this->db->table('nastaveni')->get(1);
    }

    /**
     * @param $ip
     * @return Status
     */
    public function getStatus($ip): Status {
        return new Status($ip,$this->cache);
    }

    /**
     * @param $code
     * @param int $primary
     * @return int
     */
    public function setWidgetCode($code, $primary = 1): ?int {
        return $this->db->table('widget')->wherePrimary($primary)->update([
            'code' => $code
        ]);
    }

    /**
     * @param int $primary
     * @return mixed|ActiveRow
     */
    public function getWidgetCode($primary = 1): ?string {
        return $this->db->table('widget')->get($primary)->code;
    }

    /**
     * @param $name
     * @return ActiveRow|null
     */
    public function getRow($name): ?ActiveRow {
        return $this->db->table('nastaveni')->select($name)->get(1);
    }

    /**
     * @return Explorer
     */
    public function getExplorer(): Explorer {
        return $this->db;
    }
}