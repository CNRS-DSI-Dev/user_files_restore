<?php

/**
 * ownCloud - User Files Restore
 *
 * @author Patrick Paysant <ppaysant@linagora.com>
 * @copyright 2015 CNRS DSI
 * @license This file is licensed under the Affero General Public License version 3 or later. See the COPYING file.
 */

namespace OCA\User_Files_Restore\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use OC\DB\Connection;

class RequestClean extends Command
{
    const INFO = 1; // green text (from symfony doc)
    const COMMENT = 2; // yellow text
    const QUESTION = 3; // black text on a cyan background
    const ERROR = 4; // white text on a red background

    protected $requestMapper;
    protected $output;

    public function __construct(\OCA\User_Files_Restore\Db\RequestMapper $requestMapper)
    {
        $this->requestMapper = $requestMapper;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user_files_restore:clean')
            ->setDescription('Clean already processed restoration requests (cf config.php for nb of days).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        // Cleaning
        $this->consoleDisplay('Cleaning restoration requests');
        $this->CleanRequests($output);
        $this->consoleDisplay('End');
    }

    /**
     * Clean already done migration requests, older than xxx days
     * @param OutputInterface $output
     */
    protected function CleanRequests($output)
    {
        $cleanDelay = \OCP\Config::getSystemValue('ufr_clean_delay', 7);

        $datetime = new \DateTime();
        $datetime->sub(new \dateInterval('P' . (int)$cleanDelay . 'D'));
        $datetime->setTime(23, 59, 59);

        try {
            $requests = $this->requestMapper->deleteBefore($datetime);
        }
        catch (\Exception $e) {
            $this->consoleDisplay('Server error: ' . $e->getMessage(), self::ERROR);
        }
    }

    protected function consoleDisplay($msg = '', $type = self::INFO)
    {
        $now = date('Ymd_His');
        switch($type) {
            case self::COMMENT: {
                $this->output->writeln('<comment>' . $now . ' ' . $msg . '</comment>');
                break;
            }
            case self::QUESTION: {
                $this->output->writeln('<question>' . $now . ' ' . $msg . '</question>');
                break;
            }
            case self::ERROR: {
                $this->output->writeln('<error>' . $now . ' ' . $msg . '</error>');
                break;
            }
            default: {
                $this->output->writeln('<info>' . $now . ' ' . $msg . '</info>');
            }
        }
    }
}
