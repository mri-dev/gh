<import xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://schemas.ingatlanbazar.hu/Import/ http://schemas.ingatlanbazar.hu/Import/xsd/import.xsd"
	xmlns="http://schemas.ingatlanbazar.hu/Import/">
	<officeList>
		<office ingatlanbazarId="123123">
			<information>
				<notificationmailList>
					<notificationmailText><![CDATA[info@ugyfel.hu]]></notificationmailText>
				</notificationmailList>
			</information>
			<agentList>
				<agent foreignId="agent-1da3bde1">
					<nameText>Kovács Aladár</nameText>
					<addressText>6123 Sopron Hágó u. 13.</addressText>
					<mobilenumberText>06 43 423 2322</mobilenumberText>
					<emailText>agent1@ugyfel.hu</emailText>
				</agent>
			</agentList>
			<adList>
        <ad foreignId="ad-433f6663" agentId="agent-1da3bde1">
          <type option="2"/>
          <agreement option="1"/>
          <!-- a hely megadása nevekkel -->
          <regionText>Csongrád megye</regionText>
          <cityText>Kecskemét</cityText>
          <suburbText>Belváros</suburbText>
          <address visible="true">
              <street>Hegedűs köz</street>
              <number>1</number>
          </address>
          <!-- / a hely megadása nevekkel -->
          <price intval="30000000"/>
          <currency option="1"/>
          <payingperiod option="1"/>
          <condition option="5"/>
          <heating option="1"/>
          <rooms intval="3"/>
          <halfrooms intval="1"/>
          <floorspace intval="100"/>
          <propertyspace intval="1000"/>
          <descriptionText><![CDATA[Kecskemét, Hegedűs köznél eladó egy ~100nm-es 2 éve felújított igényes családi ház, 4 szobával, 1000nm-es telekkel, gázfűtéssel.]]></descriptionText>
          <tagsText><![CDATA[Parketta, Kábel TV, Jó közlekedés]]></tagsText>
        	<imageList>
        		<image foreignId="http://www.ugyfel.hu/images/ASD9890_1.jpg" href="http://www.ugyfel.hu/images/ASD9890_1.jpg" />
        		<image foreignId="http://www.ugyfel.hu/images/ASD9890_2.jpg" href="http://www.ugyfel.hu/images/ASD9890_2.jpg" />
        	</imageList>
          <highlightingList>
              <highlighting id="1" start="2011-02-18" duration="1"/>
          </highlightingList>
        </ad>
 			</adList>
		</office>
	</officeList>
</import>