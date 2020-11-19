import React from "react";
import AbstractSearch from "./AbstractSearch";

const ProductSearch = (props) => {
    return (
        <AbstractSearch
            id={props.id}
            fieldName={props.fieldName}
            panelTitle="Search for products"
            emptyLabel="No product data found"
            fields={{id: 'ID', name: 'Name', sku: 'SKU'}}
            labelAjaxUrl={window.emailtester.productLabelAjaxUrl}
            searchAjaxUrl={window.emailtester.productSearchAjaxUrl}
        />
    );
};

export default ProductSearch;
