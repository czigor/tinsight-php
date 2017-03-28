<?php

namespace Czigor\Tinsight;

use Czigor\Tinsight\TinsightRequestBase;

/**
 * t-insight RateRequest implementation.
 */
class TinsightRateRequest extends TinsightRequestBase {


  /**
   * Customer account number to apply during processing.
   *
   * @var string
   */
  protected $uoneNumber = '';

  /**
   * An indexed array of handlingUnit arrays.
   *
   * A handlingUnit array can have the following keys:
   * - 'stackable' - Boolean. Attribute contains if units are stackable.
   * - 'Quantity' - A decimal number. Count of units within handlingUnit.
   * - 'Quantity-units' - Attribute contains HandlingUnit package type. Value
   *   can be empty but attribute must be included. Default value on processing
   *   is Pallet.
   * - 'Weight' - Decimal. Total weight of HandlingUnit.
   * - 'Weight-units' - string. Attribute contains weight measurement. Default
   *   is ‘lb’.
   * - 'Dimensions-height' - Decimal. Attribute contains height measurement of
   *   HandlingUnit.
   * - 'Dimensions-width' - Decimal. Attribute contains width measurement of
   *   HandlingUnit.
   * - 'Dimensions-length' - Decimal. Attribute contains length measurement of
   *   HandlingUnit.
   * - 'Dimensions-units' - string. The unit of dimensions.
   * - 'Items' - An indexed array of arrays. The arrays can have the following
   *   keys:
   *   - 'freightClass' - Decimal. Freight class specific to item.
   *   - 'sequence' - Numeric. Item sequence number.
   *   - 'Weight' - Decimal. Weight of item.
   *   - 'Weght-units' - String. Weight unit of item. Defaults to 'lb'.
   *   - 'Dimensions-height' - Decimal. Attribute contains height measurement of
   *     item.
   *   - 'Dimensions-width' - Decimal. Attribute contains width measurement of
   *     item.
   *   - 'Dimensions-length' - Decimal. Attribute contains length measurement of
   *     item.
   *   - 'Dimensions-units' - string. The unit of dimensions.
   *   - 'Quantity' - Decimal. Count of units within item.
   *
   * @var array
   */
  protected $handlingUnits;

  /**
   * An indexed array of Event arrays.
   *
   * An Event array can have the following keys:
   * - 'date' - Date of event in 'mm/dd/yyyy hh:mm' format.
   * - 'type' - String. Either 'Pickup' or 'Drop'.
   * - 'sequence' - Numeric. Event sequence.
   * - 'City' - String. City of event location.
   * - 'State' - String. State of event location. Limited to length of 2
   *   characters.
   * - 'Zip' - String. Postal code of event location.
   * - 'Country' - String. Country of event location. Values: 'USA',
   *   'CANADA', 'MEXICO'.
   *
   * @var array
   */
  protected $events;


  public function __construct($live = FALSE, $credentials = []) {
    parent::__construct($live, $credentials);
    $this->requestType = 'RateRequest';
  }

  /**
   * @inheritdoc
   */
  protected function requestBodyXml(\XMLWriter $writer) {
    $writer->startElement('RateRequest');
    $writer->writeAttribute('unitPricing','false');

    $writer->writeElement('uoneNumber', $this->credentials->getUsername());

    $writer->startElement('Constraints');
    $writer->writeElement('Contract');
    $writer->writeElement('Carrier');
    $writer->writeElement('Mode');

    $writer->startElement('ServiceFlags');
    $writer->startElement('ServiceFlag');
    $writer->writeAttribute('code', 'LIFT');
    $writer->text('Liftage Service');
    $writer->endElement();
    $writer->endElement();

    // End Constraints.
    $writer->endElement();

    $writer->writeElement('PaymentTerms', 'Prepaid');

    $writer->startElement('HandlingUnits');
    foreach ($this->handlingUnits as $handling_unit) {
      $writer->startElement('HandlingUnit');
      $writer->writeAttribute('stackable', $handling_unit['stackable'] ? 'true' : 'false');

      $writer->startElement('Quantity');
      $writer->writeAttribute('units', $handling_unit['Quantity-units']);
      $writer->text($handling_unit['Quantity']);
      $writer->endElement();

      $writer->startElement('Weight');
      $writer->writeAttribute('units', $handling_unit['Weight-units']);
      $writer->text($handling_unit['Weight']);
      $writer->endElement();

      $writer->startElement('Dimensions');
      $writer->writeAttribute('height', $handling_unit['Dimensions-height']);
      $writer->writeAttribute('width', $handling_unit['Dimensions-width']);
      $writer->writeAttribute('length', $handling_unit['Dimensions-length']);
      $writer->writeAttribute('units', $handling_unit['Dimensions-units']);
      $writer->endElement();

      $writer->startElement('Items');
      foreach ($handling_unit['Items'] as $item) {
        $writer->startElement('Item');
        $writer->writeAttribute('freightClass', $item['freightClass']);
        $writer->writeAttribute('sequence', $item['sequence']);

        $writer->startElement('Weight');
        $writer->writeAttribute('units', $item['Weight-units']);
        $writer->text($item['Weight']);
        $writer->endElement();

        $writer->startElement('Dimensions');
        $writer->writeAttribute('height', $item['Dimensions-height']);
        $writer->writeAttribute('width', $item['Dimensions-width']);
        $writer->writeAttribute('length', $item['Dimensions-length']);
        $writer->writeAttribute('units', $item['Dimensions-units']);
        $writer->endElement();

        $writer->startElement('Quantity');
        $writer->writeAttribute('units', $item['Quantity-units']);
        $writer->text($item['Quantity']);
        $writer->endElement();

        // End Item.
        $writer->endElement();
      }

      // End Items.
      $writer->endElement();

      // End HandlingUnit.
      $writer->endElement();
    }

    // End HandlingUnits.
    $writer->endElement();

    $writer->startElement('Events');
    foreach ($this->events as $event) {
      $writer->startElement('Event');
      $writer->writeAttribute('date', $event['date']);
      $writer->writeAttribute('type', $event['type']);
      $writer->writeAttribute('sequence', $event['sequence']);

      $writer->startElement('Location');
      $writer->writeElement('City', $event['City']);
      $writer->writeElement('State', $event['State']);
      $writer->writeElement('Zip', $event['Zip']);
      $writer->writeElement('Country', $event['Country']);

      // End Location.
      $writer->endElement();

      // End Event.
      $writer->endElement();
    }

    // End Events.
    $writer->endElement();

    // End RateRequest.
    $writer->endElement();

  }

  /**
   * handlingUnits getter.
   *
   * @return array
   */
  public function getHandlingUnits() {
    return $this->handlingUnits;
  }

  /**
   * handlingUnits setter.
   *
   * @param array $handlingUnit
   */
  public function setHandlingUnits(array $handlingUnits) {
    $this->handlingUnits = $handlingUnits;
  }

  /**
   * events getter.
   *
   * @return array
   */
  public function getEvents() {
    return $this->events;
  }

  /**
   * events setter.
   *
   * @param array $event
   */
  public function setEvents(array $events) {
    $this->events = $events;
  }

}
