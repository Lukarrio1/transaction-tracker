// import axios from "axios";

// const owner_model = document.querySelector("#owner_model");
// const owner_item = document.querySelector("#owner_item");
// const owner_model_display_aid = document.querySelector(
//     "#owner_model_display_aid"
// );
// const owned_model_display_aid = document.querySelector(
//     "#owned_model_display_aid"
// );
// const owned_item = document.querySelector("#owned_item");
// const type = document.querySelector("#type");
// const has_many = document.querySelector("#has_many");
// const references_form = document.querySelector("#references_form");
// const fd = new FormData();

// const getData = async () => {
//     const { data } = await axios.get(
//         "/references_ajax" + "?owner_model=" + fd.get("owner_model")
//     );
// };

// if (owner_model)
//     owner_model.addEventListener("change", function (e) {
//         fd.append("owner_model", e.target.value);
//         getData();
//     });
// if (owner_item)
//     owner_item.addEventListener("change", function (e) {
//         console.log(e.target.value);
//         fd.append("owner_item", e.target.value);
//     });
// if (owner_model_display_aid)
//     owner_model_display_aid.addEventListener("change", function (e) {
//         console.log(e.target.value);
//         fd.append("owner_model_display_aid", e.target.value);
//     });
// if (owned_model_display_aid)
//     owned_model_display_aid.addEventListener("change", function (e) {
//         console.log(e.target.value);
//         fd.append("owned_model_display_aid", e.target.value);
//     });

// if (type)
//     type.addEventListener("change", function (e) {
//         console.log(e.target.value);
//         fd.append("type", e.target.value);
//     });
// if (has_many)
//     has_many.addEventListener("change", function (e) {
//         console.log(e.target.value);
//         fd.append("has_many", e.target.value);
//     });
// if (owned_item)
//     owned_item.addEventListener("change", function (e) {
//         console.log(e.target.value);
//         fd.append("owned_item", e.target.value);
//     });

// if (references_form) getData();
