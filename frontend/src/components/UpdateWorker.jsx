import { useState, useEffect, useContext } from "react";
import { LoadingContext, AuthContext } from "../App";
import { useParams, useNavigate } from "react-router-dom";
import Select from 'react-select';
import makeAnimated from 'react-select/animated';

function UpdateWorker() {
    const { id } = useParams(); //ta bort sen
    if(!id) return console.error('No Param Id');
    const animatedComponents = makeAnimated();
    const {setLoading} = useContext(LoadingContext);
    const [allUnits, setAllUnits] = useState([]);
    const [unit, setUnit] = useState(null);
    const [worker, setWorker] = useState([]);//eller array
    const [selectedUnits, setSelectedUnits] = useState([]);
    const navigate = useNavigate();
      const {setIsAuth, userId, setUserId, userRole, setUserRole, userName, setUserName, checkSessionStatus} = useContext(AuthContext);

    useEffect(()=>{
         getWorker();
     }, [id]);

     useEffect(()=>{
        getAllUnits();
     }, []);
     console.log(selectedUnits);

     useEffect(() => {
        if (worker.units) {
            const formattedUnits = worker.units.map((unit) => ({
                value: unit.id,
                label: unit.name,
            }));
            setSelectedUnits(formattedUnits);
        }
    }, [worker]);

    async function handleUpdateWorker(event) {
        event.preventDefault(); 
    
        let body = {};

        const newName = event.target.worker_name.value.trim();
        const newEmail = event.target.worker_email.value.trim();
        const newPhoneNmr = event.target.worker_tel.value.trim();
        const newEmploymentType = event.target.employment_type.value;
    
        if (newName && newName !== worker.name) {
            body.newName = newName;
        }
        if (newEmail && newEmail !== worker.email) {
            body.newEmail = newEmail;
        }
        if (newPhoneNmr && newPhoneNmr !== worker.phoneNmr) {
            body.newPhoneNmr = newPhoneNmr;
        }
        if (newEmploymentType && newEmploymentType !== worker.employmentType) {
            body.newEmploymentType = newEmploymentType;
        }


        let newSelectedUnits = selectedUnits.map(unit=>(unit.value));
        let oldUnitValues = worker.units.map(unit=>(unit.id));
        newSelectedUnits.sort((a,b)=> a-b );
        oldUnitValues.sort((a,b)=> a-b );
        
        oldUnitValues = oldUnitValues.toString();
        newSelectedUnits=newSelectedUnits.toString();

        console.log("HGRDSAFSDFSD HÄR E JAG");
        console.log(newSelectedUnits);
        console.log(oldUnitValues);

        let result = newSelectedUnits === oldUnitValues ? true : false
        console.log("RESULT: " + result);
        if(result === false){
            body.newUnitIds = selectedUnits.map(unit => unit.value);
        }

        
        console.log(body); 
    
        try {
            if (Object.keys(body).length > 0) { 
                const res = await fetch('http://localhost/mini-axami/public/api/updateWorker/' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
    
                if (!res.ok) {
                    return console.log('Fetch failed');
                }
    
                const jsonRes = await res.json();
    
                if (jsonRes.error) {
                    return console.log('Error: ' + jsonRes.error);
                }
    
                console.log(jsonRes.success);
                setWorker(jsonRes.success);
                navigate('/showWorkers')
            } else {
                console.log("Inga ändringar att uppdatera.");
            }
        } catch (error) {
            console.error("Error at catch: " + error);
        }
    }
    

    async function getWorker(){
        try{
            const res = await fetch('http://localhost/mini-axami/public/api/getWorker/'+id,{
                method:'GET',
                headers: { 'Content-Type': 'application/json'},
            });
            const jsonRes = await res.json();

            if(!res.ok) return console.log('fetch failed');

            console.log(jsonRes['success']);

            if(jsonRes['error']) return console.log(jsonRes.error || 'Server Error');

            setWorker(jsonRes.success);
            
        }catch(error){
            console.log('Error ' + error);
        }
    }

    async function getAllUnits(){ /* I SERVERN HÄMTA FRÅN SESSION VILKET FÖRETAG. HÄMTA UNITS FÖR FÖRETAGET ENBART */
        try{
            const response = await fetch('http://localhost/mini-axami/public/api/getAllCompanyUnits',{
                method:'GET',
                headers: { 'Content-Type': 'application/json' },
            });
            const serverRes = await response.json();

            if(!response.ok){
               return console.log(serverRes.error || "Hämtning av units misslyckades. Försök igen.");
            }

            console.log(serverRes['success']);
            if (serverRes['success']){
              setAllUnits(serverRes.success);
              setUserName(worker.name); //BEHÖVS KANSKE EJ.
            } else {
                console.log(serverRes.error || "Server Error occurred...");
            }
        }catch (error) {
            console.error("Fel vid hämtning av Units:", error);
           
        }
    }
    function handleCancel(){
        console.log("Canceled");
        return navigate('/workerRegister');
    }

    return ( 
    <>
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Uppdatera Worker</h3>
                <form onSubmit={handleUpdateWorker} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Nytt Förnamn & Efternamn</label>
                        <input className="form-control" type="text" name="worker_name" placeholder={worker.name} />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Ny Email</label>
                        <input className="form-control" type="email" name="worker_email" placeholder={worker.email} />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Nytt telefonnummer</label>
                        <input className="form-control" type="tel" name="worker_tel" placeholder={worker.phoneNmr} /> {/* kan lägga till pattern på tel type */}
                    </div>

                    <label className="form-label">Arbetarens Units:</label>
                    {allUnits ? 
                        <Select
                            isMulti
                            name="unitChoice"
                            options={allUnits.map(unit => ({ value: unit.id, label: unit.name }))} //Behöver vara key value pair
                            className="basic-multi-select"
                            classNamePrefix="Välj Units"
                            components={animatedComponents} 
                            value={selectedUnits}
                            onChange={setSelectedUnits}
                        />
                        : <></>
                    }
                    <div className="mb-3">
                        <label className="form-label">Anställningstyp</label>
                        <select className="form-select" name="employment_type">
                            <option value={worker.employmentType}>Nuvarande: {worker.employmentType}</option>
                            <option value="Tillsvidareanställning">Tillsvidareanställning</option>
                            <option value="Visstidsanställning">Visstidsanställning</option>
                            <option value="Provanställning">Provanställning</option>
                            <option value="Konsult ">Konsult</option>
                            <option value="Timanställning">Timanställning</option>
                        </select>
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Uppdatera</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    
    </> );
}

export default UpdateWorker;