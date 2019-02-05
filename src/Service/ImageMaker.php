<?php
declare(strict_types=1);

namespace App\Service;


use Intervention\Image\ImageManager;

class ImageMaker
{
    private const CANVAS_HEIGHT = 600;
    private const CANVAS_WIDTH = 600;

    private const BADGE_HEIGHT = self::CANVAS_HEIGHT / 5;
    private const BADGE_WIDTH = self::CANVAS_WIDTH / 2;

    private $intervention;

    public function __construct(ImageManager $intervention)
    {
        $this->intervention = $intervention;
    }

    public function make(\SplFileInfo $file, $colors): \SplFileInfo
    {
        $image = $this->intervention->canvas(self::CANVAS_WIDTH, self::CANVAS_HEIGHT);
//        $image->resizeCanvas($image->width(), $image->height());
        foreach ($colors as $index => $color) {
            [$colorValue, $count] = $color;
            [$x1, $y1, $x2, $y2] = $this->getRectanglePosition($index);
            $image->rectangle($x1, $y1, $x2, $y2, function ($draw) use ($colorValue) {
                $draw->background($colorValue);
                $draw->border(6, '#fff');
            });
        }
        $newFile = new \SplFileInfo($file->getPath() . '/' . uniqid('exported_', false));
        $image->save($newFile->getPathname());
        return $newFile;
    }

    public function getRectanglePosition(int $index): array
    {
        if (($index + 1) % 2 === 0) {
            $x1 = 0;
            $x2 = self::BADGE_WIDTH;
        } else {
            $x1 = self::BADGE_WIDTH + 1;
            $x2 = self::CANVAS_WIDTH;
        }
        $y1 = intdiv($index, 2) * self::BADGE_HEIGHT;
        $y2 = $y1 + self::BADGE_HEIGHT;
        return [$x1, $y1, $x2, $y2];
    }
}
