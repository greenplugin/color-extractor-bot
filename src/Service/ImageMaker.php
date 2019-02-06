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

    private $canvasHeight;

    private $intervention;

    public function __construct(ImageManager $intervention)
    {
        $this->intervention = $intervention;
    }

    public function make(\SplFileInfo $file, $colors): \SplFileInfo
    {
        $this->canvasHeight = self::BADGE_HEIGHT * (intdiv(count($colors), 2) + count($colors) % 2) + 1;
        $image = $this->intervention->canvas(self::CANVAS_WIDTH, $this->canvasHeight);
//        $image->resizeCanvas($image->width(), $image->height());
        foreach ($colors as $index => $color) {
            [$x1, $y1, $x2, $y2] = $this->getRectanglePosition($index, count($colors));
            $image->rectangle($x1, $y1, $x2, $y2, function ($draw) use ($color) {
                $draw->background($color);
                $draw->border(1, '#fff');
            });
        }
        $newFile = new \SplFileInfo($file->getPath() . '/' . uniqid('exported_', false));
        $image->save($newFile->getPathname());
        return $newFile;
    }

    public function getRectanglePosition(int $index, $count): array
    {
        $y1 = intdiv($index, 2) * self::BADGE_HEIGHT;
        $y2 = $y1 + self::BADGE_HEIGHT;
        if (($index + 1) % 2 !== 0) {
            $x1 = 1;
            $x2 = self::BADGE_WIDTH - 1;
            if ($index + 1 === $count) {
                $x2 = self::CANVAS_WIDTH - 1;
            }
            return [$x1, $y1, $x2, $y2];
        }

        $x1 = self::BADGE_WIDTH;
        $x2 = self::CANVAS_WIDTH - 1;

        return [$x1, $y1, $x2, $y2];
    }
}
