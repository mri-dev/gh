<import xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://schemas.ingatlanbazar.hu/Import/ http://schemas.ingatlanbazar.hu/Import/xsd/import.xsd"
	xmlns="http://schemas.ingatlanbazar.hu/Import/">
	<officeList>
		<office ingatlanbazarId="<?=$feed->account_id?>">
			<information>
				<notificationmailList>
					<notificationmailText><![CDATA[<?=$feed->contactEmail()?>]]></notificationmailText>
				</notificationmailList>
			</information>
			<agentList>
				<?php foreach ($feed->agents() as $agent): ?><agent foreignId="<?=$agent['ID']?>">
					<nameText><?=$agent['name']?></nameText>
					<mobilenumberText><?=$agent['phone']?></mobilenumberText>
					<emailText><?=$agent['email']?></emailText>
				</agent>
				<?php endforeach; ?>
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
				<?php
				$properties = $feed->properties();
				$ie = 0;
				foreach ($properties as $p) {
					$ie++;
					$regions = $p->Regions();
					$regions = array_reverse($regions);
					$city = $regions[0]->name;
					$sub = false;
					$megye = end($regions)->name;

					if ( in_array($city, array('Kecskemét', 'Kiskunhalas')) )
					{
						$megye = 'Csongrád';
					}

					if($regions[1]->name == 'Budapest') {
						$sub = $city;
						$city = $regions[1]->name;
					}
				?>
				<ad foreignId="<?=$p->Azonosito()?>" agentId="<?=$p->AuthorID()?>">
					<regionText><?=$megye?> megye</regionText>
          <cityText><?=$city?></cityText>
					<? if($sub): ?>
					<suburbText><?=$sub?></suburbText>
					<? endif; ?>
					<type option="<?=$feed->typeConverter($p->PropertyType())?>"/>
					<descriptionText><![CDATA[<?=strip_tags(html_entity_decode($p->Description()))?>]]></descriptionText>
					<price intval="<?=$p->Price()?>"/>
					<currency option="1"/>
					<payingperiod option="<?=$feed->periodConverter($p->PriceTypeID())?>"/>
					<agreement option="<?=$feed->StatusConverter($p->StatusID())?>"/>
					<?php // TODO: CONDITION ?>
					<condition option=""/>
					<?php // TODO: Heating ?>
					<heating option=""/>
					<?php $rooms = $feed->rooms($p); if($rooms): ?>
					<rooms intval="<?=$rooms?>"/><? endif; ?>
					<?php $halfrooms = $feed->halfrooms($p); if($halfrooms): ?>
					<halfrooms intval="<?=$halfrooms?>"/><? endif; ?>
					<?php $floorspace = $feed->floorspace($p); if($floorspace): ?>
					<floorspace intval="<?=$floorspace?>"/><? endif; ?>
					<?php $propertyspace = $feed->propertyspace($p); if($propertyspace): ?>
					<propertyspace intval="<?=$propertyspace?>"/><? endif; ?>
					<imageList>
						<?php
							$images = $p->Images();
							if($images)
							foreach ($images as $i) { ?>
								<image foreignId="<?=$i->ID?>" href="<?=$i->guid?>" />
							<? } ?>
        	</imageList>
				</ad>
				<? } ?>
 			</adList>
		</office>
	</officeList>
</import>
