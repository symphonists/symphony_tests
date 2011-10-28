<?php

	/**
	* XSLT Process tests
	*
	* Tests to ensure the XSLTProcess class works as expected
	*/
	class SymphonyTestXSLTProcess extends UnitTestCase {

		public $xsd = 'null';

		public function setUp() {
			require_once TOOLKIT . '/class.xsltprocess.php';

			$this->xsd = '<?xml version="1.0"?>
			<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified">
			<xs:element name="beers">
			 <xs:complexType>
			  <xs:sequence>
			   <xs:element name="beer">
			    <xs:complexType>
			     <xs:sequence>
			     <xs:element name="name" type="xs:string" />
			     <xs:element name="origin" type="xs:string" minOccurs="1" />
			     </xs:sequence>
			    </xs:complexType>
			   </xs:element>
			  </xs:sequence>
			 </xs:complexType>
			</xs:element>
			</xs:schema>';
		}

		public function testValidateFalse() {
			// Invalid XML
			$xml = '<beers>
			 <beer>
			  <name>Corona</name>
			 </beer>
			</beers>';

			$XSLTProcess = new XSLTProcess($xml);

			$this->assertFalse($XSLTProcess->validate($this->xsd));
			$this->assertTrue($XSLTProcess->isErrors());
			$this->assertEqual(1, count($XSLTProcess->getError(true)));
		}

		public function testValidateTrue() {
			// Valid XML
			$xml = '<beers>
			 <beer>
			  <name>Corona</name>
			  <origin>Mexico</origin>
			 </beer>
			</beers>';

			$XSLTProcess = new XSLTProcess($xml);

			$this->assertTrue($XSLTProcess->validate($this->xsd));
			$this->assertFalse($XSLTProcess->isErrors());
			$this->assertEqual(0, count($XSLTProcess->getError(true)));
		}
	}