import $ from 'jquery'

export default class Resource {
    constructor($endpoint) {
        this.endpoint = $endpoint;
    }

    static getEndpoint () {
        return this.endpoint;
    }

    get ($parameters, $callback) {
        $.get(this.endpoint, $parameters, $callback, "json");
    }
}

