<?php
namespace Dizda\CloudBackupBundle\Tests\Manager;

use Dizda\CloudBackupBundle\Client\ClientInterface;
use Dizda\CloudBackupBundle\Client\DownloadableClientInterface;
use Dizda\CloudBackupBundle\Manager\ClientManager;
use Psr\Log\LoggerInterface;

class ClientManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldExecuteDownloadForFirstDownloadableClient()
    {
        $clients = [];
        $clients[] = $this->getMock(ClientInterface::class);
        $clientMock = $this->getMock(DownloadableClientInterface::class);
        $fileMock = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setConstructorArgs([tempnam(sys_get_temp_dir(), '')])
            ->getMock();
        $clientMock->expects($this->once())->method('download')->willReturn($fileMock);
        $clients[] = $clientMock;

        $clientManager = new ClientManager($this->getMock(LoggerInterface::class), $clients);
        $this->assertSame($fileMock, $clientManager->download());
    }

    /**
     * @test
     * @expectedException \Dizda\CloudBackupBundle\Exception\MissingDownloadableClientsException
     * @expectedExceptionMessage No downloadable client is registered.
     */
    public function shouldThrowExceptionIfNoChildIsADownloadableClient()
    {
        $clients = [];
        $clients[] = $this->getMock(ClientInterface::class);
        $clients[] = $this->getMock(ClientInterface::class);

        $clientManager = new ClientManager($this->getMock(LoggerInterface::class), $clients);
        $clientManager->download();
    }
}
