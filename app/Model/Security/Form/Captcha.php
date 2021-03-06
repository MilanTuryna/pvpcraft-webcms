<?php


namespace App\Model\Security\Form;

use App\Constants;
use Nette\SmartObject;

/**
 * Class Captcha
 * @package App\Model\Security
 */
final class Captcha
{
    use SmartObject;

    const freqMethods = [
      'No' => ['Neni', 'Ne', 'Nie', 'n', 'nn', 'none', "nikoliv"],
      'Yes' => ['Ano', 'Je', 'Jo', 'j', 'jj', 'hej', 'samozrejme', 'jiste']
    ];

    const methods = [
        'Kolik dní je 48 hodin?' => ['2', '2 dny', 'Dva', 'Dva dny'],
        'Je slovo "pes" sloveso?' => self::freqMethods['No'],
        'Jaké je krajské město pardubického kraje?' => ['Pardubice'],
        'Jaké je hlavní město České Republiky?' => ['Praha', 'Prague'],
        'Je Frankfurt v česku?' => self::freqMethods['No'],
        'Je černá tmavá barva?' => self::freqMethods['Yes'],
        'Je slunce hvězda nebo planeta?' => ['Hvezda', 'Hvezd', 'Hvezdy'],
        'Žijeme v 19. století nebo 21. století?' => ['21.', '21'],
        'Je Praha na slovensku?' => self::freqMethods['No'],
        'Je Praha v česku?' => self::freqMethods['Yes'],
        'Je Minecraft hra?' => self::freqMethods['Yes'],
        'Potřebujete na jízdu automobilem řidičský průkaz?' => self::freqMethods['Yes'],
        'Potřebujete na cyklistické kolo řidičský průkaz?' => self::freqMethods['No'],
        'Patří facebook mezi sociální sítě?' => self::freqMethods['Yes'],
        'Vytváří zapalovač oheň?' => self::freqMethods['Yes'],
        'Je New York v německu?' => self::freqMethods['No'],
        'Je Youtube fotobanka?' => self::freqMethods['No'],
        'Patří instagram mezi sociální sítě?' => self::freqMethods['Yes'],
        'Patří twitter mezi sociální sítě?' => self::freqMethods['Yes'],
        'Lední medvěd je hnědý?' => self::freqMethods['No'],
        'Patří slimák mezi rychlé zvířata?' => self::freqMethods['No'],
        'Patří mravenec mezi velké zvířata?' => self::freqMethods['No'],
        'Je Beatles zpěvecká skupina?' => self::freqMethods['Yes'],
        'Je Koronavirus virus?' => self::freqMethods['Yes'],
    ];

    private string $method;
    private array $methodAnswers;

    /**
     * Captcha constructor.
     * @param $method
     */
    public function __construct($method)
    {
        $this->method = $method;
        $this->methodAnswers = array_map(fn($data) => strtr(strtolower($data), Constants::VALID_URL), self::methods[$this->method]);
    }

    /**
     * @return string
     */
    public static function getRandomMethod(): string {
        return (string)array_rand(self::methods);
    }

    /**
     * @param $method
     * @return false|int|string
     */
    public static function getMethodOrder($method) {
        return array_search($method, array_keys(Captcha::methods));
    }

    /**
     * @return mixed
     */
    public function getMethod(): string {
        return $this->method;
    }

    /**
     * @param $input
     * @return bool
     */
    public function verify($input): bool {
        return in_array(strtr(mb_strtolower($input), Constants::VALID_URL), $this->methodAnswers);
    }
}