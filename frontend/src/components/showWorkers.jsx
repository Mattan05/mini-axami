import { useState, useEffect, useContext } from "react";
import { Link,useNavigate } from "react-router-dom";
import { LoadingContext } from "../App";

function showWorkers() {
    const [allWorkers, setAllWorkers] = useState([]);
    const {setLoading} = useContext(LoadingContext);
    const navigate = useNavigate();

    const handleUpdate = (worker) => {
        navigate('/updateWorker/'+worker.id);
    };

    useEffect(()=>{
        getAllWorkers();
    }, [])

    async function getAllWorkers(){
        try{
            const res = await fetch('http://localhost/mini-axami/public/api/getAllCompanyWorkers',{
                method:'GET',
                headers:{'Content-Type' : 'application/json'},
            }); 
    
            const jsonRes = await res.json();

            if(!res.ok) return console.log("Fetch failed");

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

    async function handleDelete(worker) {
        setLoading(true);
        if (window.confirm("Är du säker på att du vill avskeda "+worker.name+"?")) {
            try {
                console.log("INSIDE HANDELDELETE")
                const res = await fetch(`http://localhost/mini-axami/public/api/worker/delete/${worker.id}`, {
                    method: "POST",
                    headers:{'Content-Type': 'application/json'}
                });

                if (!res.ok) {
                    console.log("Kunde inte avskeda workern.");
                }

                alert(worker.name + " är nu avskedad");
                navigate("/showWorkers");
            } catch (error) {
                console.log("Fel vid radering: " + error.message);
            }finally{
                setLoading(false);
            }
        }
    };

    const unitPage = (event) => {
        console.log(event.target.id.replace("user_",""));
        navigate('/unit/'+ event.target.id.replace("user_",""));
    }
    return ( <>
        {allWorkers ? 
        <>{/* updateWorker */}
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
              
                    <strong>Units:</strong> {worker.units.length > 0 ? worker.units.map(w=>(<p className="text-primary" style={{cursor:'pointer'}} onClick={unitPage} key={w.id} id={`user_${w.id}`}>{w.name}</p>)) : "Inga"} <br />
                    {/* <strong>Uppgifter:</strong> {worker.unitTasks.length > 0 ? worker.unitTasks.join(", ") : "Inga"} */}
               
                </div>
                <button className="btn btn-warning" onClick={() => handleUpdate(worker)}>Uppdatera Uppgifter</button>
                <button className="btn btn-danger" onClick={() => handleDelete(worker)}>Avskeda Arbetare</button>

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