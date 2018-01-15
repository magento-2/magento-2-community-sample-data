# Magento Shipping Extension

A multi-carrier shipping extension for Magento 2.

## Description

The Magento Shipping extension integrates a growing number of shipping carriers into Magento 2.

## Getting Started

The following steps are required to configure the Magento Shipping extension.

### Activate Magento Shipping

A Magento Shipping account must be created at the [Magento Shipping Portal](https://shipping.magento.com/) in order to use the extension. To access the portal, you must first log in to your [Magento account](https://account.magento.com).

### Configure Magento Shipping

Registration provides you with an API endpoint and API credentials to configure the extension. Enter these into the _Magento Shipping_ section of the the _Shipping Methods_ configuration page, which is located at:

```
Stores → Configuration → Sales → Shipping Methods
```

Once the API connection is established successfully, complete the following configuration below the main _Magento Shipping_ configuration section:

1. Locations: Shipping origin addresses.
1. Carriers: Connections to carriers.
1. Packaging: Pre-configured containers.
1. Shipping Experiences: Shipping methods and rates. This redirects to the Magento Shipping Portal.

Once this configuration is complete, enable Magento Shipping for checkout using the following drop-down setting in the main _Magento Shipping_ configuration section:

```
Stores → Configuration → Sales → Shipping Methods → Magento Shipping → Enabled: Yes
```

## Support

For Magento Shipping extension support, go to the Magento Shipping [Help Centre](https://magentoshipping.temando.com).

## Credits

_Magento Shipping_ is a collaborative work of [Temando Pty Ltd.](http://temando.com) and [Netresearch GmbH & Co. KG](https://www.netresearch.de/), leveraging Temando API capabilities within the Magento® e-commerce platform's order processing workflow.

## License

For license information, see [LICENSE.txt](LICENSE.txt).
