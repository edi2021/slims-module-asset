<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-15 15:49:31
 * @modify date 2021-08-15 15:49:31
 * @desc [description]
 */

namespace SLiMSAssetmanager\Migration;

class Engine
{
    private $Path;
    private array $Seed;
    private object $ReadyState;

    private function __construct($Path)
    {
        $this->Path = $Path;
    }

    private function getSeed(): Engine
    {
        foreach ( array_diff(scandir($this->Path), ['.','..']) ?? [] as $File) {
            if (preg_match('/.php/i', $File) && !is_dir($File))
            {
                $this->Seed[] = substr_replace($File, '', strpos($File, '.'));
            }
        }
        rsort($this->Seed);
        return $this;
    }   

    public function pullClutch(int $GearPosition = 0): Engine
    {
        $this->getSeed();
        
        if (!count($this->Seed)) 
        {
            return false;
        }

        $Gear = "SLiMSAssetmanager\Migration\Seed\\" . $this->Seed[$GearPosition];
        $this->ReadyState = new $Gear();

        return $this;
    }

    public function openThrottle()
    {
        $this->ReadyState->up();
    }
    
    public static function ignite(string $Path): Engine
    {
        return new Engine($Path);
    }
}