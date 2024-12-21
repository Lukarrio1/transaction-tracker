export function SameLevelJoinFields({
    mainTable,
    MainTable,
    setBaseLevelTables,
    query_conditions,
    columns,
    mainColumns,
    node,
}) {
    return (
        <>
            <div className="text-center h4">{mainTable}: start</div>
            <div class="mb-3">
                <label
                    for={`node_join_${MainTable}_to_${mainTable}_by_column`}
                    class="form-label"
                >
                    Node {MainTable} Column To Join By{" "}
                </label>
                <button
                    class="btn btn-danger btn-sm h4"
                    title="Remove join entry"
                    onClick={(e) => {
                        e.preventDefault();
                        setBaseLevelTables((pre) => [
                            ...pre?.filter((c) => c != mainTable),
                        ]);
                    }}
                >
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <select
                    id={`node_join_${MainTable}_to_${mainTable}_by_column`}
                    class="form-select"
                    name={`node_join_${MainTable}_to_${mainTable}_by_column`}
                    onChange={(e) => {}}
                >
                    <option value="">Select column</option>
                    {mainColumns &&
                        mainColumns.map((column) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value[
                                            `node_join_${MainTable}_to_${mainTable}_by_column`
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
                <label
                    for={`node_join_${MainTable}_to_${mainTable}_by_condition`}
                    class="form-label"
                >
                    Node {mainTable} join by condition
                </label>
                <select
                    id={`node_join_${MainTable}_to_${mainTable}_by_condition`}
                    class="form-select"
                    name={`node_join_${MainTable}_to_${mainTable}_by_condition`}
                    required
                >
                    <option value="">Select join by condition</option>
                    {query_conditions &&
                        query_conditions.map((condition) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value[
                                            `node_join_${MainTable}_to_${mainTable}_by_condition`
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
                <label
                    for={`node_join_${mainTable}_to_${MainTable}_by_column`}
                    class="form-label"
                >
                    Node {mainTable} join column
                </label>
                <select
                    id={`node_join_${mainTable}_to_${MainTable}_by_column`}
                    class="form-select"
                    name={`node_join_${mainTable}_to_${MainTable}_by_column`}
                    required
                >
                    <option value="">Select join by column</option>
                    {columns &&
                        columns.map((column) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value[
                                            `node_join_${mainTable}_to_${MainTable}_by_column`
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
                <label
                    for={`node_join_${mainTable}_to_${MainTable}_by_type`}
                    class="form-label"
                >
                    Object, Array or Count
                </label>
                <select
                    id={`node_join_${mainTable}_to_${MainTable}_by_type`}
                    class="form-select"
                    name={`node_join_${mainTable}_to_${MainTable}_by_type`}
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
                                selected={
                                    node?.properties?.value[
                                        `node_join_${mainTable}_to_${MainTable}_by_type`
                                    ] == item.value
                                }
                                value={item.value}
                            >
                                {item.key}
                            </option>
                        );
                    })}
                </select>
            </div>
            <div className="text-center h4">{mainTable}: end</div>
        </>
    );
}
