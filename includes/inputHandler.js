// ref: https://stackoverflow.com/questions/10683712/html-table-sort

// const table = document.querySelector(".special-weatherforecast-table"); //get the table to be sorted
// console.log('test', table);

// // table.querySelectorAll('th') // get all the table header elements
// //   .forEach((element, columnNo)=>{ // add a click handler for each
// //     element.addEventListener('click', event => {
// //         sortTable(table, columnNo); //call a function which sorts the table by a given column number
// //     })
// //   })

// function sortTable(table, sortColumn){
//     // get the data from the table cells
//     const tableBody = table;
//     const tableData = table2data(tableBody);
//     // sort the extracted data
//     tableData.sort((a, b)=>{
//       if(a[sortColumn] > b[sortColumn]){
//         return 1;
//       }
//       return -1;
//     })
//     // put the sorted data back into the table
//     data2table(tableBody, tableData);
//   }

// // this function gets data from the rows and cells
// // within an html tbody element
// function table2data(tableBody){
//     const tableData = []; // create the array that'll hold the data rows
//     tableBody.querySelectorAll('tr')
//       .forEach(row=>{  // for each table row...
//         const rowData = [];  // make an array for that row
//         row.querySelectorAll('td')  // for each cell in that row
//           .forEach(cell=>{
//             rowData.push(cell.innerText);  // add it to the row data
//           })
//         tableData.push(rowData);  // add the full row to the table data
//       });
//     return tableData;
//   }

//   // this function puts data into an html tbody element
//   function data2table(tableBody, tableData){
//     tableBody.querySelectorAll('tr') // for each table row...
//       .forEach((row, i)=>{
//         const rowData = tableData[i]; // get the array for the row data
//         row.querySelectorAll('td')  // for each table cell ...
//           .forEach((cell, j)=>{
//             cell.innerText = rowData[j]; // put the appropriate array element into the cell
//           })
//       });
//   }



function sortTable(table_id, sortColumn){
    var tableData = document.getElementById(table_id).getElementsByTagName('tbody').item(0);
    var rowData = tableData.getElementsByTagName('tr');
    for(var i = 0; i < rowData.length - 1; i++){
        for(var j = 0; j < rowData.length - (i + 1); j++){
            if(Number(rowData.item(j).getElementsByTagName('td').item(sortColumn).innerHTML.replace(/[^0-9\.]+/g, "")) < Number(rowData.item(j+1).getElementsByTagName('td').item(sortColumn).innerHTML.replace(/[^0-9\.]+/g, ""))){
                tableData.insertBefore(rowData.item(j+1),rowData.item(j));
            }
        }
    }
}
  //sortTable("special-weatherforecast-table", 1);