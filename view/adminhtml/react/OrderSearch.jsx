import React from "react";
import AbstractSearch from "./AbstractSearch";

const OrderSearch = (props) => {
    return (
        <AbstractSearch
            id={props.id}
            fieldName={props.fieldName}
            panelTitle="Search for orders"
            emptyLabel="No order data found"
            fields={{
                id: 'ID',
                increment_id: 'Increment ID',
                customer_email: 'Customer Email',
                created_at: 'Created At'
            }}
            labelAjaxUrl={window.emailtester.orderLabelAjaxUrl}
            searchAjaxUrl={window.emailtester.orderSearchAjaxUrl}
        />
    );
};

export default OrderSearch;
