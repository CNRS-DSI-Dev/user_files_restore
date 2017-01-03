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
use Symfony\Component\Console\Helper\Table as TableHelper;

use OC\DB\Connection;

class RequestList extends Command
{
    const INFO = 1; // green text (from symfony doc)
    const COMMENT = 2; // yellow text
    const QUESTION = 3; // black text on a cyan background
    const ERROR = 4; // white text on a red background

    protected $requestMapper;
    protected $output;
    protected $searchedStatus;

    public function __construct(\OCA\User_Files_Restore\Db\RequestMapper $requestMapper)
    {
        $this->requestMapper = $requestMapper;
        $this->searchedStatus = null;
        $this->csv = FALSE;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('user_files_restore:list')
            ->setDescription('List restoration requests.')
            ->addOption('status', 's', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Status of the request: 1(TODO), 2(RUNNING), 3(DONE).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        // -- status option
        $this->searchedStatus = $input->getOption('status');

        foreach ($this->searchedStatus as $key => $searchedStatus) {
            if ($searchedStatus != 1 AND $searchedStatus != 2 AND $searchedStatus != 3) {
                unset($this->searchedStatus[$key]);
            }
        }

        // Listing
        $this->consoleDisplay('Listing restoration requests');
        $this->listRequests($output);
        $this->consoleDisplay('The End');
    }

    /**
     * Displays list of confirmed migration requests
     * @param OutputInterface $output
     */
    protected function listRequests($output)
    {
        if (empty($this->searchedStatus) or in_array(1, $this->searchedStatus)) {
            $this->consoleDisplay('TODO restore requests');
            $todos = $this->listTodos();
            if (!empty($todos)) {
                $table = new TableHelper($output);
                $table->setStyle('borderless');
                $table->setHeaders(array('requestId', 'userId', 'Path', 'Version', 'Creation date'));
                $rows = array();
                foreach($todos as $request) {
                    $row = array($request->getId(), $request->getUid(), $request->getPath(), $request->getVersion(), $request->getDateRequest());
                    array_push($rows, $row);
                }
                $table->setRows($rows);
                $table->render();
            }
            else {
                $this->consoleDisplay('No request found!');
            }
        }

        if (empty($this->searchedStatus) or in_array(2, $this->searchedStatus)) {
            $this->consoleDisplay('RUNNING restore requests');
            $runnings = $this->listRunnings();
            if (!empty($runnings)) {
                $table = new TableHelper($output);
                $table->setLayout(TableHelper::LAYOUT_BORDERLESS);
                $table->setHeaders(array('requestId', 'userId', 'Path', 'Version', 'Creation date'));
                $rows = array();
                foreach($runnings as $request) {
                    $row = array($request->getId(), $request->getUid(), $request->getPath(), $request->getVersion(), $request->getDateRequest());
                    array_push($rows, $row);
                }
                $table->setRows($rows);
                $table->render();
            }
            else {
                $this->consoleDisplay('No request found!');
            }
        }

        if (empty($this->searchedStatus) or in_array(3, $this->searchedStatus)) {
            $this->consoleDisplay('DONE restore requests');
            $dones = $this->listDones();
            if (!empty($dones)) {
                $table = new TableHelper($output);
                $table->setLayout(TableHelper::LAYOUT_BORDERLESS);
                $table->setHeaders(array('requestId', 'userId', 'Path', 'Version', 'Creation date', 'Ending date'));
                $rows = array();
                foreach($dones as $request) {
                    $row = array($request->getId(), $request->getUid(), $request->getPath(), $request->getVersion(), $request->getDateRequest(), $request->getdateEnd());
                    array_push($rows, $row);
                }
                $table->setRows($rows);
                $table->render();
            }
            else {
                $this->consoleDisplay('No request found!');
            }
        }
    }

    protected function listTodos()
    {
        try {
            $requests = $this->requestMapper->getRequests(null, \OCA\User_Files_Restore\Db\RequestMapper::STATUS_TODO);
            return $requests;
        }
        catch (\Exception $e) {
            $this->consoleDisplay('Server error: ' . $e->getMessage(), self::ERROR);
            return array();
        }
    }

    protected function listRunnings()
    {
        try {
            $requests = $this->requestMapper->getRequests(null, \OCA\User_Files_Restore\Db\RequestMapper::STATUS_RUNNING);
            return $requests;
        }
        catch (\Exception $e) {
            $this->consoleDisplay('Server error: ' . $e->getMessage(), self::ERROR);
            return array();
        }
    }

    protected function listDones()
    {
        try {
            $requests = $this->requestMapper->getRequests(null, \OCA\User_Files_Restore\Db\RequestMapper::STATUS_DONE);
            return $requests;
        }
        catch (\Exception $e) {
            $this->consoleDisplay('Server error: ' . $e->getMessage(), self::ERROR);
            return array();
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
