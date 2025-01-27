<?php

namespace App\Service;

use Symfony\Component\Clock\Clock;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/** Сервис для сохранения файлов */
class File
{
    /**
     * Метод сохранения файлов
     * @param UploadedFile $file загружаемый файл
     * @param string $path путь для сохранения
     * @param SluggerInterface $slugger
     * @return string
     */
    public function save(UploadedFile $file, string $path, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $dir = Clock::get()->now()->format('dmY');
        $newFilename = $dir . '/' . md5($safeFilename) . '.' . $file->guessExtension();
        try {
            $file->move(
                $path . '/' . $dir,
                $newFilename
            );
        } catch (FileException $e) {
            /** TODO поправить ошибку при загрузке */
        }
        return $newFilename;
    }

}