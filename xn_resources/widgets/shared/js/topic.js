dojo.provide('xg.shared.topic');

/**
 * System for publishing events and subscribing to them.
 */
xg.shared.topic = {

    /** Map of topic to arrays of listeners. */
    listeners: {},

    /**
     * Notifies the listeners for a topic.
     *
     * @param topic  the name of the event
     * @param args  the arguments to pass to each listener
     */
    publish: function(topic, args) {
        if (! this.listeners[topic]) { this.listeners[topic] = []; }
        dojo.lang.forEach(this.listeners[topic], function(listener) {
            listener.apply(listener, args);
        });
    },

    /**
     * Adds a listener to a topic.
     *
     * @param topic  the name of the event
     * @param listener  the callback function
     */
    subscribe: function(topic, listener) {
        if (! this.listeners[topic]) { this.listeners[topic] = []; }
        this.listeners[topic].push(listener);
    },

    /**
     * Removes a listener from a topic.
     *
     * @param topic  the name of the event
     * @param listener  the callback function
     */
    unsubscribe: function(topic, listener) {
        if (! this.listeners[topic]) { this.listeners[topic] = []; }
        this.listeners[topic] = dojo.lang.filter(this.listeners[topic], function(otherListener) { return listener !== otherListener; });
    }

}
