import { createQueryString } from "./nodeform";
import { SameLevelJoinFields } from "./SameLevelJoinFields";

export function SameLevelJoin({
    mainTable,
    mainTables,
    mainColumns,
    node,
    query_conditions,
    database,
    level = 0,
}) {
    const [node_base_level_join_tables, setBaseLevelTables] = React.useState(
        []
    );
    const [mainTableColumns, setMainTableColumns] = React.useState(null);

    const getTableData = async () => {
        const { data } = await axios.get(
            "/node/databus/tableData?" +
                createQueryString({
                    tables: node_base_level_join_tables ?? [level],
                    database,
                })
        );
        setMainTableColumns(data?.tables_with_columns);
    };

    React.useEffect(() => {
        getTableData();
    }, [node_base_level_join_tables]);

    React.useEffect(() => {
        if (!node) return;
        if (node?.properties?.value[`node_base_level_join_tables_${mainTable}`])
            setTimeout(
                () =>
                    setBaseLevelTables(
                        JSON.parse(
                            node?.properties?.value[
                                `node_base_level_join_tables_${mainTable}`
                            ]
                        )
                    ),
                1000
            );
    }, [node]);

    return (
        <>
            <div class="mb-3">
                <label for={`node_base_level_join_table`} class="form-label">
                    Node Tables To Join on {mainTable} level{" "}
                    {JSON.stringify(node_base_level_join_tables)}
                </label>
                <select
                    id={`node_base_level_join_tables_${mainTable}`}
                    class="form-select"
                    name={`node_base_level_join_tables_${mainTable}`}
                    onChange={(e) => {
                        setBaseLevelTables([
                            ...node_base_level_join_tables?.filter(
                                (c, idx) => c != e.target.value
                            ),
                            e.target.value,
                        ]);
                    }}
                >
                    <option value="">Select table</option>
                    {mainTables &&
                        mainTables.map((table) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value
                                            ?.node_base_level_join_table ==
                                        table
                                    }
                                    value={table}
                                >
                                    {table}
                                </option>
                            );
                        })}
                </select>
            </div>
            {node_base_level_join_tables.length > 0 &&
                mainTableColumns != null &&
                node_base_level_join_tables.map((nd, idx) => {
                    return (
                        <SameLevelJoinFields
                            mainTable={nd}
                            MainTable={mainTable}
                            setBaseLevelTables={setBaseLevelTables}
                            query_conditions={query_conditions}
                            database={database}
                            columns={mainTableColumns[nd]}
                            mainColumns={mainColumns}
                            node={node}
                        ></SameLevelJoinFields>
                    );
                })}
            <input
                type="hidden"
                value={JSON.stringify(node_base_level_join_tables ?? [])}
                name={`node_base_level_join_tables_${mainTable}`}
            />
        </>
    );
}
