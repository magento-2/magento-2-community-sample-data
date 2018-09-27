## [1.3.3] - 2018-08-28
### Fixed

- [`#17363`](https://github.com/magento/magento2/issues/17363) Improve sanity check before accessing shipping method property

## [1.3.2] - 2018-08-17
### Fixed

- Improve performance
- Display collection point experiences only after a delivery location was chosen

## [1.3.1] - 2018-08-07
### Fixed

- Consider virtual products in multi shipping checkout
- Prevent exception when unable to display a Shipment’s capability value

### Added

- Delivery Options for UPS
    - Adult Signature
    - Direct Delivery

### Changed

- Improved error messages for dispatch errors
- Improved error messages during booking a shipment with _Magento Shipping_

## [1.3.0] - 2018-07-23
### Added

- Bulk Booking of Shipments

## [1.2.9] - 2018-07-13
### Fixed

- Consider partially shipped orders during shipment synchronization
- Download shipping labels to database if shipment was created through auto-processing or shipment synchronization
- UI display issues:
  - Enhance dispatch error messages
  - Add customer reference number
  - Add _Delivery Availability_ capability
  - Add UPS _High Value Report_ documentation

## [1.2.8] - 2018-06-15
### Fixed

- Limit shipping methods after collection point selection

## [1.2.7] - 2018-06-08
### Fixed

- Comparison of value-added shipping services in multishipping checkout
- Confirmation messages in grid delete actions

## [1.2.6] - 2018-05-31
### Fixed

- Connection name fallback in single-master setups

## [1.2.5] - 2018-05-17
### Added

- Display module version number in shipping method configuration

### Fixed

- Support multi-master setup ([split database performance solution](https://devdocs.magento.com/guides/v2.2/config-guide/multi-master/multi-master.html)).
- Render collection point add-on on shipment details page

## [1.2.4] - 2018-05-04
### Fixed

- Hide return shipment table heading if no rows are displayed
- Update result message if no collection points were found
- Show only regular addresses if no collection point was chosen during checkout
- Fix loading virtual orders

### Changed

- Save value-added shipping services in checkout through dedicated webapi endpoint

## [1.2.3] - 2018-04-27
### Fixed

- Checkout component loading error
- Display documentation on dispatch details page
- Display addresses from platform on shipment details page

## [1.2.2] - 2018-04-23
### Fixed

- Support apostrophes in customer addresses
- Limit shipping methods when collection point was chosen
- Display selected collection point in checkout sidebar
- Format collection point opening hours

## [1.2.1] - 2018-04-16
### Fixed

- Adapt to collection point API changes
- Display selected collection point in admin panel

## [1.2.0] - 2018-04-11
### Added

- Pre-Booked Returns: Automatically create return shipment labels for new shipments
- Auto-Processing: Automatically create shipments for incoming orders
- Collection Points: Enable customers to collect parcels from a drop point

## [1.1.3] - 2018-03-27
### Fixed

- Hide RMA return shipments tab when order was not shipped with _Magento Shipping_.

## [1.1.2] - 2018-03-08
### Fixed

- Remove selection column from return shipments grid with no mass action.

## [1.1.1] - 2018-03-02
### Added

- Server-side pagination for Dispatch grid

## [1.1.0] - 2018-02-28
### Added

- Create *Ad-hoc Return* labels with return shipment tracking (builds upon `Magento_Rma`)
- Validate that package weight is less than packaging max weight on order ship page
- Display additional details on shipment view page

### Fixed

- [`#12921`](https://github.com/magento/magento2/issues/12921) Perform type check on extension attributes during quote address updates
- Enable componentry loading in IE 11
- Use base currency in order qualification requests
- Remove duplicate navigation bar from carrier registration page

## [1.0.4] - 2017-12-06
### Fixed

- Complete error in previous release reverting zend-code v3.2.0 compatibility

## [1.0.3] - 2017-12-06
### Revert

- Establish compatibility to zend-code package v3.2.0 and up

### Fixed

- Sustain backwards compatibility in estimate-shipping-methods-by-address-id REST API call

## [1.0.2] - 2017-12-05
### Fixed

- Establish compatibility to zend-code package v3.2.0 and up

## [1.0.1] - 2017-12-05
### Changed

- Update merchant onboarding link

## [1.0.0] - 2017-12-04
### Fixed

- Consider admin token lifetime for REST API access

## [0.3.9] - 2017-12-01
### Changed

- Display fixed location value in tracking popup progress details

## [0.3.8] - 2017-12-01
### Fixed

- Change token type for REST API access

## [0.3.7] - 2017-11-25
### Fixed

- Prevent componentry JS from being minified twice

## [0.3.6] - 2017-11-21
### Fixed

- Remove duplicate timezone calculation in tracking popup
- Consider line item discount in order requests

## [0.3.5] - 2017-11-14
### Added

- Validate credentials in shipping method configuration

### Fixed

- Refresh shipping rates on address changes in checkout
- Add billing address in order requests
- Add product categories in order requests
- Show number of selected grid rows

## [0.3.4] - 2017-10-26
### Added

- Select value-added shipping services in multishipping checkout

### Fixed

- Read selected mass action IDs in grid listings
- Add page size option in grid listings

## [0.3.3] - 2017-10-19
### Changed

- Update support link in module configuration
- Dispay activation notice in config area

### Fixed

- Consider _Show Method if Not Applicable_ config setting
- Action button URL in locations grid

## [0.3.2] - 2017-10-02
### Changed

- Establish Magento® 2.2.0 compatibility, drop 2.1.x compatibility

### Fixed

- Select value-added shipping services in guest checkout

## [0.3.1] - 2017-09-26
### Security

- Sanitize input, escape output

## [0.3.0] - 2017-09-18
### Added

- Synchronize shipment entities created from 3rd party systems (e.g. WMS)
- Select value-added shipping services in checkout
- Display packaging details on _View Shipment_ page
- Display API entity IDs on _View Order_ and _View Shipment_ page
- Include guide to handle dispatch problems
- Set API credentials in module config section
- Delete registered carriers, locations, and containers from merchant account
- Edit registered carriers

### Changed

- Move merchant onboarding info (activation, getting started) to module config section
- Use localized endpoints after initial API authentication
- Error Logging (always log errors, add response headers)
- Extend _My Carriers_ grid columns
- Display carrier name instead of shipping method name in tracking popup

### Removed

- Tracking link in shipment confirmation email
- Merchant account registration (moved to external platform)

### Fixed

- Adapt API schema changes
