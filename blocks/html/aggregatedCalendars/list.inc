<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Aggregation $this->aggregation
 */
use Application\Models\Person;

$helper = $this->template->getHelper('buttonLink');
?>
<section>
    <h1><?= $this->_(['aggregatedCalendar', 'aggregatedCalendars', count($this->calendars)]); ?></h1>
    <div class="tools">
    <?php
        if (Person::isAllowed('aggregatedCalendars', 'update')) {
            echo $helper->buttonLink(
                self::generateUri('aggregatedCalendars.update').'?aggregation_id='.$this->aggregation->getId(),
                $this->_('aggregatedCalendar_add'),
                'add'
            );
        }

        if (Person::isAllowed('aggregations', 'sync')) {
            echo $helper->buttonLink(
                self::generateUri('aggregations.sync', ['id'=>$this->aggregation->getId()]),
                $this->_('synchronize'),
                'synchronize'
            );
        }
    ?>
    </div>
    <table>
    <?php
        foreach ($this->aggregation->getAggregatedCalendars() as $a) {
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