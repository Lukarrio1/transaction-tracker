import { createQueryString } from "./nodeform";
import { SameLevelJoin } from "./SameLevelJoin";
import TableToJoin from "./TableToJoin";

export default function JoinTablesForm({
    mainColumns,
    node,
    mainTables,
    database,
    MainTable,
}) {
    const [selectedTables, setSelectedTables] = React.useState([]);
    const [tablesToJoin, setTablesToJoin] = React.useState({});
    const [query_conditions, setQueryConditions] = React.useState([]);
    const [ttl, setTtl] = React.useState(0);

    const getTableData = async () => {
        const { data } = await axios.get(
            "/node/databus/tableData?" +
                createQueryString({ tables: selectedTables ?? "", database })
        );
        setTablesToJoin(data.tables_with_columns);
        setQueryConditions(data.query_conditions);
    };

    React.useEffect(() => {
        getTableData();
    }, [selectedTables]);

    React.useEffect(() => {
        if (!node) return;
        if (!node?.properties?.value?.node_join_tables) return;
        setSelectedTables(
            JSON.parse(node?.properties?.value?.node_join_tables)
        );
        setTtl(node?.properties?.value?.node_cache_ttl);
    }, [node]);

    return (
        <>
            <div class="mb-3">
                <div className="card">
                    <div className="card-body text-center h4">
                        Use with caution, this is still in beta. (leave fields
                        blank if you don't intend to use nested joins, if so try
                        to keep your joins to a reasonable amount eg 3 levels
                        deep).
                    </div>
                </div>
            </div>
            {/* <div class="mb-3">
                <label for={`node_cache_ttl`} class="form-label">
                    Node Endpoint Cache Time To Live{" "}
                    <bold> ({ttl ?? 0} seconds)</bold>
                </label>
                <input
                    type="number"
                    class="form-control"
                    id={`node_cache_ttl`}
                    aria-describedby="node_name"
                    name={`node_cache_ttl`}
                    onChange={(e) => setTtl(e.target.value)}
                    placeholder={"0"}
                />
                <input
                    type="hidden"
                    aria-describedby="node_name"
                    name={`node_cache_ttl`}
                    placeholder={"0"}
                    value={ttl}
                />
            </div> */}
            <div class="mb-3">
                <label for="node_join_column" class="form-label">
                    Node Column To Join By
                </label>
                <select
                    id="node_join_column"
                    class="form-select"
                    name="node_join_column"
                    onChange={(e) => {}}
                >
                    <option value="">Select column</option>
                    {mainColumns &&
                        mainColumns.map((column) => {
                            return (
                                <option
                                    selected={
                                        node?.properties?.value
                                            ?.node_join_column == column
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
                    Node Tables To Join To {JSON.stringify(selectedTables)}
                    {/* {[
                        "App\\Http\\Controllers\\Api\\DataBusController::saveRecord",
                        "App\\Http\\Controllers\\Api\\DataBusController::updateRecord",
                    ]?.includes(route_function_value?.split("_")[0])
                        ? JSON.stringify(columns_to_save)
                        : ""} */}
                </label>
                <select
                    id="node_join_tables"
                    class="form-select"
                    // name="node_join_tables"
                    onChange={(e) => {
                        setSelectedTables([
                            ...selectedTables?.filter(
                                (c, idx) => c != e.target.value
                            ),
                            e.target.value,
                        ]);
                    }}
                >
                    <option value="">Select table</option>
                    {mainTables &&
                        mainTables.map((table) => {
                            return <option value={table}>{table}</option>;
                        })}
                </select>
            </div>
            <input
                type="hidden"
                value={JSON.stringify(selectedTables)}
                name={"node_join_tables"}
            />
            {/* <SameLevelJoin
                mainColumns={mainColumns}
                mainTables={mainTables}
                mainTable={MainTable}
                node={node}
                query_conditions={query_conditions}
                database={database}
                level={MainTable}
            ></SameLevelJoin> */}
            {Object.keys(tablesToJoin)?.map(function (key) {
                return (
                    key.length > 0 && (
                        <TableToJoin
                            table_columns={tablesToJoin}
                            table={key}
                            columns={tablesToJoin[key]}
                            node={node}
                            query_conditions={query_conditions}
                            setSelectedTables={setSelectedTables}
                            MainTable={MainTable}
                            mainTables={mainTables}
                            database={database}
                        ></TableToJoin>
                    )
                );
            })}
        </>
    );
}
