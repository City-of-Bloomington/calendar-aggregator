<?php
/**
 * @copyright 2015-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Aggregation $this->aggregation
 */
use Application\Models\Person;

$helper    = $this->template->getHelper('buttonLink');
$calendars = $this->aggregation->getAggregatedCalendars();
$id        = $this->aggregation->getId();
$name      = parent::escape($this->aggregation->getName());
?>
<section>
    <h1><?= $name; ?></h1>
    <div class="tools">
    <?php
        if (Person::isAllowed('aggregatedCalendars', 'update')) {
            echo $helper->buttonLink(
                self::generateUri('aggregatedCalendars.update').'?aggregation_id='.$id,
                $this->_('aggregatedCalendar_add'),
                'add'
            );
        }

        if (Person::isAllowed('aggregations', 'sync')) {
            echo $helper->buttonLink(
                self::generateUri('aggregations.sync', ['id'=>$id]),
                $this->_('synchronize'),
                'synchronize'
            );
        }
    ?>
    </div>
    <table>
    <?php
        foreach ($calendars as $a) {
            $name      = self::escape($a->getName());
            $google_id = self::escape($a->getGoogle_calendar_id());

            $deleteButton = '';
            if (Person::isAllowed('aggregatedCalendars', 'delete')) {
                $deleteButton = $helper->buttonLink(
                    self::generateUri('aggregatedCalendars.delete', ['id'=>$a->getId()]),
                    $this->_('aggregatedCalendar_delete'),
                    'delete'
                );
            }

            echo "
            <tr><td>$name</a></td>
                <td>$google_id</a></td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
