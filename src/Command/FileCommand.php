<?php


namespace App\Command;


use App\Dto\SumDto;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\NumberFormatException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileCommand extends Command
{
    private const
        DEFAULT_DIRECTORY = 'public/files';
    /**
     * @var string|string[]|null
     */
    private string $directory;

    protected function configure()
    {
        ini_set('track_errors', 1);
        ini_set('memory_limit', -1);

        $this->setName('file')
            ->setDescription('Show sum in files')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'path to the file'
            )
            ->addArgument(
                'directory',
                InputArgument::OPTIONAL,
                'path to the directory',
                self::DEFAULT_DIRECTORY
            )
            ->setHelp('This command prints the files and sum');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->directory = $input->getArgument('directory');
        $path            = $input->getArgument('path');

        $files_list = $this->getSum($path);

        foreach ($files_list as $sum_dto)
        {
            $output->writeln($sum_dto->path . ' - ' . $sum_dto->sum);
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $path
     *
     * @return SumDto[]
     * @throws \Exception
     * @throws NumberFormatException
     */
    private function getSum(string $path): array
    {
        if (file_exists($this->getFullPath($path)))
        {
            if ($file = fopen($this->getFullPath($path), "r"))
            {
                // initialise variables
                $files_list = [];
                $sum        = BigDecimal::of(0);

                while (!feof($file))
                {
                    // remove new line chars and get new line
                    $line = str_replace(["\n", "\r"], '', fgets($file));

                    // if the line is empty
                    if (empty($line))
                    {
                        continue;
                    }

                    // if link to another file
                    if (file_exists($this->getFullPath($line)))
                    {
                        $files = $this->getSum($line);

                        $sum        = $sum->plus(BigDecimal::of($files[0]->sum));
                        $files_list = array_merge($files_list, $files);
                    }
                    // there is a number ot it will throw a NumberFormatException
                    else
                    {
                        $sum = $sum->plus(BigDecimal::of($line));
                    }
                }
                fclose($file);
                // adding file to the top of stack
                array_unshift($files_list, new SumDto($path, $sum));

                return $files_list;
            }
            else
            {
                // throw an Exception with an error from fopen
                throw new Exception(error_get_last());
            }
        }
        else
        {
            throw new Exception('File ' . $this->getFullPath($path) . ' not found');
        }
    }

    private function getFullPath(string $filename): string
    {
        return join(DIRECTORY_SEPARATOR, [$this->directory, $filename]);
    }
}