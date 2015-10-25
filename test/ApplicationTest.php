<?php
/**
 * Sainsbury's test
 *
 * @author Anton Zagorskii amberovsky@gmail.com
 */

namespace SainsburyTest;

use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use Sainsbury\Application;
use Sainsbury\CurlWrapper;
use Sainsbury\Parser;

/**
 * Application Test
 */
class ApplicationTest extends PHPUnit_Framework_TestCase {

    public function testFetchUrlCalledWithProperUrlWhenFetchingPageSource() {
        /** @var PHPUnit_Framework_MockObject_MockObject|Parser $ParserMock */
        $ParserMock = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFruits'])
            ->getMock();

        $ParserMock
            ->expects($this->once())
            ->method('getFruits')
            ->willReturn([]);

        /** @var PHPUnit_Framework_MockObject_MockObject|CurlWrapper $CurlWrapperMock */
        $CurlWrapperMock = $this->getMockBuilder(CurlWrapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|Application $ApplicationMock */
        $ApplicationMock = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([$CurlWrapperMock, $ParserMock])
            ->setMethods(['fetchURL'])
            ->getMock();

        $ApplicationMock
            ->expects($this->once())
            ->method('fetchURL')
            ->with($this->equalTo('test'));

        $ApplicationMock->run('test');
    }

    public function testFetchURLThrowsExceptionWhenCurlWrapperReturnedFalse() {
        $this->setExpectedException(RuntimeException::class);

        /** @var PHPUnit_Framework_MockObject_MockObject|Parser $ParserMock */
        $ParserMock = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|CurlWrapper $CurlWrapperMock */
        $CurlWrapperMock = $this->getMockBuilder(CurlWrapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['exec'])
            ->getMock();

        $CurlWrapperMock
            ->expects($this->once())
            ->method('exec')
            ->willReturn(false);

        /** @var PHPUnit_Framework_MockObject_MockObject|Application $ApplicationMock */
        $ApplicationMock = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([$CurlWrapperMock, $ParserMock])
            ->setMethods(null)
            ->getMock();

        $ApplicationMock->run('test');
    }

    public function testFetchURLThrowsExceptionWhenErrnoReturnsNonZeroValue() {
        $this->setExpectedException(RuntimeException::class);

        /** @var PHPUnit_Framework_MockObject_MockObject|Parser $ParserMock */
        $ParserMock = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|CurlWrapper $CurlWrapperMock */
        $CurlWrapperMock = $this->getMockBuilder(CurlWrapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['errno'])
            ->getMock();

        $CurlWrapperMock
            ->expects($this->once())
            ->method('errno')
            ->willReturn(1);

        /** @var PHPUnit_Framework_MockObject_MockObject|Application $ApplicationMock */
        $ApplicationMock = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([$CurlWrapperMock, $ParserMock])
            ->setMethods(null)
            ->getMock();

        $ApplicationMock->run('test');
    }

    public function testParserCalledWithProperValuesAndAllDetailsAreRequestedFromParser() {
        /** @var PHPUnit_Framework_MockObject_MockObject|Parser $ParserMock */
        $ParserMock = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFruits', 'parseFruitDescription'])
            ->getMock();

        $ParserMock
            ->expects($this->any())
            ->method('parseFruitDescription')
            ->with($this->equalTo('detailed1Data'))
            ->willReturn('description');

        $ParserMock
            ->expects($this->once())
            ->method('getFruits')
            ->with(
                $this->equalTo('testData'),
                $this->callback(function ($subject) {
                    return
                        is_callable($subject) &&
                        ($subject('detailed1') == ['description', mb_strlen('detailed1Data')]);
                }))
            ->willReturn([]);

        /** @var PHPUnit_Framework_MockObject_MockObject|CurlWrapper $CurlWrapperMock */
        $CurlWrapperMock = $this->getMockBuilder(CurlWrapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var PHPUnit_Framework_MockObject_MockObject|Application $ApplicationMock */
        $ApplicationMock = $this->getMockBuilder(Application::class)
            ->setConstructorArgs([$CurlWrapperMock, $ParserMock])
            ->setMethods(['fetchURL'])
            ->getMock();

        $ApplicationMock
            ->expects($this->exactly(3)) // in fact it should be 2, but there is a bug in PHPUnit
            ->method('fetchURL')
            ->withConsecutive(['test'], ['detailed1'], ['detailed1'])
            ->willReturnOnConsecutiveCalls('testData', 'detailed1Data', 'detailed1Data');

        $ApplicationMock->run('test');
    }
}
