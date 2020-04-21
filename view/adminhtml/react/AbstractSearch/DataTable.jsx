import React, {useState, useEffect} from "react";

const DataTable = (props) => {
    const items = props.items;
    const fields = props.fields;

    let i = 0;

    return (
        <table className="data-grid">
            <thead>
            <tr>
                {Object.keys(fields).map((fieldCode) => (
                    <th key={fieldCode} className="data-grid-multicheck-cell">{'' + fields[fieldCode]}</th>
                ))}
                <th className="data-grid-multicheck-cell">Actions</th>
            </tr>
            </thead>
            <tbody>
            {items.map((item) => {
                i++;
                let classNames = [];
                classNames.push("data-row");
                classNames.push(i % 2 ? "_even_row" : "_odd_row");
                return (
                    <tr key={item.id} className={classNames}>
                        {Object.keys(fields).map(fieldCode => (
                            <td key={fieldCode} onClick={() => props.onRowClick(item.id)}>
                                {'' + item[fieldCode]}
                            </td>
                        ))}
                        <td>
                            <button type="button" onClick={() => props.onRowClick(item.id)}>
                                <span>Select</span>
                            </button>
                        </td>
                    </tr>
                );
            })}
            {(items.length > 0) || <tr className="data-row"><td colSpan="4">No items found</td></tr>}
            </tbody>
        </table>
    )
};

export default DataTable;
