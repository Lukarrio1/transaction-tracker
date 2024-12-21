import { getPreviousElement } from "./nodeform";
import { SameLevelJoin } from "./SameLevelJoin";

export default function TableToJoin({
    table_columns,
    columns,
    table,
    node,
    query_conditions,
    setSelectedTables,
    MainTable,
    mainTables,
    database,
}) {
    const previousElement = getPreviousElement(table_columns, table);
    return (
        <>
            <hr />
            {previousElement?.key != null && (
                <div class="mb-3">
                    <label for="node_join_column" class="form-label">
                        Node {previousElement?.key ?? MainTable} Column To Join
                        By
                    </label>
                    <select
                        id={`node_previous_${table}_join_column`}
                        class="form-select"
                        name={`node_previous_${table}_join_column`}
                        required
                    >
                        <option value="">Select column</option>
                        {previousElement?.obj &&
                            previousElement?.obj?.map((column) => {
                                return (
                                    <option
                                        selected={
                                            node?.properties?.value[
                                                `node_previous_${table}_join_column`
                                            ] == column
                                        }
                                        value={column}
                                    >
                                        {column}
                                    </option>
                                );
                            })}
                    </select>
                </div>
            )}
            <div class="mb-3">
                <label for="node_join_by_column" class="form-label">
                    Join {table} By Condition{" "}
                    <button
                        class="btn btn-danger btn-sm h4"
                        title="Remove join entry"
                        onClick={(e) => {
                            e.preventDefault();
                            setSelectedTables((pre) => [
                                ...pre?.filter((c) => c != table),
                            ]);
                        }}
                    >
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </label>
                <select
                    id={`node_${table}_join_by_condition`}
                    class="form-select"
                    name={`node_${table}_join_by_condition`}
                    required
                >
                    <option value="">Select join by condition</option>
                    {query_conditions &&
                        query_conditions.map((condition) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value[
                                            `node_${table}_join_by_condition`
                                        ] == condition
                                    }
                                    value={condition}
                                >
                                    {condition}
                                </option>
                            );
                        })}
                </select>
            </div>
            <div class="mb-3">
                <label for="node_join_by_column" class="form-label">
                    Join {table} to {previousElement?.key ?? MainTable} By
                </label>
                <select
                    id={`node_${table}_join_by_column`}
                    class="form-select"
                    name={`node_${table}_join_by_column`}
                    required
                >
                    <option value="">Select join by column</option>
                    {columns &&
                        columns.map((column) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value[
                                            `node_${table}_join_by_column`
                                        ] == column
                                    }
                                    value={column}
                                >
                                    {column}
                                </option>
                            );
                        })}
                </select>
            </div>
            <div class="mb-3">
                <label for="node_table" class="form-label">
                    Node {table} Columns To Display
                </label>
                <select
                    id="node_table_columns"
                    class="form-select"
                    name={`node_${table}_join_columns[]`}
                    multiple={true}
                    required
                >
                    <option value="">Select A Table Columns</option>
                    {columns &&
                        columns?.map((column) => {
                            return (
                                <option
                                    selected={node?.properties?.value[
                                        `node_${table}_join_columns`
                                    ]?.includes(column)}
                                    value={column}
                                >
                                    {column}
                                </option>
                            );
                        })}
                </select>
            </div>
            <div class="mb-3">
                <label
                    for={`node_${table}_object_or_array_or_count`}
                    class="form-label"
                >
                    Object, Array or Count
                </label>
                <select
                    id={`node_${table}_object_or_array_or_count`}
                    class="form-select"
                    name={`node_${table}_object_or_array_or_count`}
                    required
                >
                    <option value="">Select an option</option>
                    {[
                        { key: "Array", value: 2 },
                        { key: "Object", value: 1 },
                        { key: "Count", value: 3 },
                    ].map((item) => {
                        return (
                            <option
                                value={item.value}
                                selected={
                                    node?.properties?.value[
                                        `node_${table}_object_or_array_or_count`
                                    ] == item.value
                                }
                            >
                                {item.key}
                            </option>
                        );
                    })}
                </select>
            </div>
            {/* <SameLevelJoin
                mainTables={mainTables}
                mainTable={table}
                mainColumns={columns}
                node={node}
                query_conditions={query_conditions}
                database={database}
            ></SameLevelJoin> */}
        </>
    );
}
