import { useState, useEffect } from "react";

function showWorkers() {
    const [allWorkers, setAllWorkers] = useState([]);

    useEffect(()=>{
        getAllWorkers();
    }, [])

    async function getAllWorkers(){
        try{
            let res = await fetch('http://localhost/mini-axami/public/api/getAllCompanyWorkers');

            if(!res.ok) return console.log("Fetch failed");

            let jsonRes = await res.json();

            if(jsonRes['success']){
                console.log(jsonRes['success']);
                setAllWorkers(jsonRes['success']);
            }else{
                return console.error("ERROR: " + jsonRes['error'])
              }
        }catch(error){
            console.error("Fel vid hämtning av workers:", error);
        }
    }
    return ( <>
        {allWorkers ? 
        <>
        {allWorkers.map(worker => (
            <div className="card mb-4 d-block" key={worker.id} style={{ width: "18rem" }}>
                <div className="card-body">
                <h5 className="card-title">{worker.name}</h5>
                <h6 className="card-subtitle mb-2 text-muted">ID: {worker.id}</h6>
                <p className="card-text">
                    <strong>Email:</strong> {worker.email} <br />
                    <strong>Roller:</strong> {worker.roles.join(", ")} <br />
                    <strong>Telefon:</strong> {worker.phoneNmr} <br />
                    <strong>Anställningstyp:</strong> {worker.employmentType} <br />
                </p>
                <p>
                    <strong>Units:</strong> {worker.units.length > 0 ? worker.units.map(w=>(<a key={w.id} href={w.id}>{w.name}</a>)) : "Inga"} <br />
                    {/* <strong>Uppgifter:</strong> {worker.unitTasks.length > 0 ? worker.unitTasks.join(", ") : "Inga"} */}
                </p>
                </div>
            </div>
        ))}

            <div className="card">

            </div>
        </> 
        :  
        <p>Inga workers hittades...</p>}
        
    </> );
}

export default showWorkers;