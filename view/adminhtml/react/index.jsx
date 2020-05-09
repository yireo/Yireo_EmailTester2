import React from "react";
import ReactDOM from "react-dom";
import CustomerSearch from './CustomerSearch';
import ProductSearch from './ProductSearch';
import OrderSearch from './OrderSearch';

const componentMapping = {
    CustomerSearch,
    ProductSearch,
    OrderSearch
};

/*
import initiateReactComponents from "@yireo/magento2-react-adminhtml";
initiateReactComponents(componentMapping);
*/

window.renderReactComponent = (componentId, elementId, props = {}) => {
    if (!elementId || !componentId) {
        throw new Error('renderReactComponent requires 2 arguments');
    }

    const Component = componentMapping[componentId];
    ReactDOM.render(
        <Component {...props} />,
        document.getElementById(elementId),
    );
};
