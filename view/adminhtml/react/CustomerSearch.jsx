import React from "react";
import AbstractSearch from "./AbstractSearch";

const CustomerSearch = (props) => {
    return (
        <AbstractSearch
            id={props.id}
            panelTitle="Search for customers"
            emptyLabel="No customer data found"
            fields={{id: 'ID', name: 'Name', email: 'Email'}}
            labelAjaxUrl={window.emailtester.customerLabelAjaxUrl}
            searchAjaxUrl={window.emailtester.customerSearchAjaxUrl}
        />
    );
};

export default CustomerSearch;
