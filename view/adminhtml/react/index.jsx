import React from "react";
import ReactDOM from "react-dom";
import CustomerSearch from "./CustomerSearch";
import ProductSearch from "./ProductSearch";
import OrderSearch from "./OrderSearch";

window.renderReactComponent = (componentId, elementId, props = {}) => {
    if (!elementId || !componentId) {
        throw new Error('renderReactComponent requires 2 arguments');
    }

    let Component;
    switch(componentId) {
        case 'CustomerSearch':
            Component = CustomerSearch;
            break;
        case 'ProductSearch':
            Component = ProductSearch;
            break;
        case 'OrderSearch':
            Component = OrderSearch;
            break;
        default:
            throw new Error('renderReactComponent is called with unknown component identifier')
    }

    ReactDOM.render(
        <Component {...props} />,
        document.getElementById(elementId),
    );
};
