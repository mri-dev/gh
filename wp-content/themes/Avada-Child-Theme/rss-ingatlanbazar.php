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
					<?php $gps =$p->GPS(); if($gps): ?>
					<coordinates latitude="<?=$gps['lat']?>" longitude="<?=$gps['lng']?>"/>
					<? endif; ?>
					<type option="<?=$feed->typeConverter($p->PropertyType())?>"/>
					<descriptionText><![CDATA[<?=strip_tags(html_entity_decode($p->Description()))?>]]></descriptionText>
					<price intval="<?=$p->Price()?>"/>
					<currency option="1"/>
					<payingperiod option="<?=$feed->periodConverter($p->PriceTypeID())?>"/>
					<agreement option="<?=$feed->StatusConverter($p->StatusID())?>"/>
					<?php $condition = $feed->conditionConverter($p->ConditionID()); if($condition): ?>
					<condition option="<?=$condition?>"/><? endif; ?>
					<?php $heating = $feed->heatingConverter($p->HeatingID()); if($heating): ?>
					<heating option="<?=$heating?>"/><? endif; ?>
					<?php $rooms = $feed->rooms($p); if($rooms): ?>
					<rooms intval="<?=$rooms?>"/><? endif; ?>
					<?php $halfrooms = $feed->halfrooms($p); if($halfrooms): ?>
					<halfrooms intval="<?=$halfrooms?>"/><? endif; ?>
					<?php $floorspace = $feed->floorspace($p); if($floorspace): ?>
					<floorspace intval="<?=$floorspace?>"/><? endif; ?>
					<?php $propertyspace = $feed->propertyspace($p); if($propertyspace): ?>
					<propertyspace intval="<?=$propertyspace?>"/><? endif; ?>
					<imageList>
						<?php $profil = $p->ProfilImg(); if($profil): ?>
							<image foreignId="<?=$p->ProfilImgID()?>" href="<?=$profil?>" />
						<? endif; ?>
						<?php
							$images = $p->Images();
							if($images)
							foreach ($images as $i) { if($p->ProfilImgID() == $i->ID) continue; ?>
								<image foreignId="<?=$i->ID?>" href="<?=$i->guid?>" />
							<? } ?>
        	</imageList>
				</ad>
				<? } ?>
 			</adList>
		</office>
	</officeList>
</import>
