/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

define(
    [
        'underscore',
        'Magento_Customer/js/customer-data',
        'Magento_Ui/js/model/messageList'
    ],
    function(_, customerData, messageContainer) {
        /**
         * A utility for observing message updates in session storage. It is designed to subscribe to
         * customer data updates and forward messages to the appropriate messageList model.
         */
        return function() {
            var typeMap = {
                'success'   : 'addSuccessMessage',
                'warning'   : 'addErrorMessage',
                'error'     : 'addErrorMessage'
            };

            /**
             * Observe message section data changes and forward to the error processor.
             * @param Object data The observable payload.
             * @return void
             */
            var messageSubscriptionCallback = function(data) {
                if ('messages' in data) {
                    _.each(data.messages, function(message) {
                        if (message.type in typeMap) {
                            messageContainer[typeMap[message.type]]({
                                'message': message.text
                            });
                        }
                    });
                }
            };

            customerData.get('messages').subscribe(messageSubscriptionCallback);
        };
    }
);
