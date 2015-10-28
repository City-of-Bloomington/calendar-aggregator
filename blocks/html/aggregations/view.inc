<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Aggregation $this->aggregation
 */
use Application\Models\Person;

$name = self::escape($this->aggregation->getName());
$google_calendar_id = self::escape($this->aggregation->getGoogle_calendar_id());

$editButton = '';
if (Person::isAllowed('aggregations', 'update')) {
    $helper = $this->template->getHelper('buttonLink');
    $editButton = $helper->buttonLink(
        self::generateUri('aggregations.update', ['id'=>$this->aggregation->getId()]),
        $this->_('aggregation_edit'),
        'edit'
    );
}
?>
<section>
    <h1><?= $name; ?></h1>
    <div class="tools"><?= $editButton; ?></div>
    <h2><?= $google_calendar_id; ?></h2>
</section>