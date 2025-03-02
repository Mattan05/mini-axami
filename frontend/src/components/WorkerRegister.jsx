import React, {useContext, useEffect, useState} from 'react';
import { LoadingContext } from '../App';
import { useNavigate } from "react-router-dom";
import Select from 'react-select';
import makeAnimated from 'react-select/animated';


function WorkerRegister() {
    const animatedComponents = makeAnimated();
    let navigate = useNavigate();
    const {setLoading} = useContext(LoadingContext);
    /* const [allCompanies, setAllCompanies] = useState([]); */
    const [allUnits, setAllUnits] = useState([]);
    const [selectedUnits, setSelectedUnits] = useState([]);


   /*  useEffect(()=>{
        getAllCompanies();
    },[]); */

    useEffect(()=>{
        getAllUnits();
    },[]);

    async function handleRegistration(event) {
        event.preventDefault();
/* workerUnits: selectedUnits.map(unit => unit.value) */
        setLoading(true);
        /*   setErrorMessage(null);  */

        const body = JSON.stringify({
            worker_name: event.target.worker_name.value,
            worker_tel: event.target.worker_tel.value,
            worker_email: event.target.worker_email.value,
            employment_type: event.target.employment_type.value,
            workerUnits: selectedUnits.map(unit => unit.value), //En array med id retuneras
        });
       
         console.log(body);

        try {
            let res = await fetch('http://localhost/mini-axami/public/api/registerWorker', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: body
            });
            console.log(res);

            if (!res.ok) {
                console.log(res.statusText || "Registrering av Worker misslyckades. Försök igen."); /* ['error'] */
            }

            const serverRes = await res.json();
            
            console.log(serverRes);
            if (serverRes['success']) {
                return navigate("/showWorkers");
            } else {
                console.log(serverRes.error || "Server Error occurred...");
            }

        } catch (error) {
            console.error("Fel vid registrering av worker:", error);
            /*  setErrorMessage(error.message); */
        }finally{
            setLoading(false);
        }
    }

    

        async function getAllUnits(){ /* I SERVERN HÄMTA FRÅN SESSION VILKET FÖRETAG. HÄMTA UNITS FÖR FÖRETAGET ENBART */
            try{
                let response = await fetch('http://localhost/mini-axami/public/api/getAllCompanyUnits',{
                    method:'GET',
                    headers: { 'Content-Type': 'application/json' },
                });
                let serverRes = await response.json();
    
                if(!response.ok){
                    console.log(serverRes.error || "Hämtning av units misslyckades. Försök igen.");
                }
    
                console.log(serverRes['success']);
                if (serverRes['success']){
                  setAllUnits(serverRes.success);
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

    return ( <>
  {/*   <h3 className='text-light'>{JSON.stringify(allUnits)}</h3> */}
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Registrera Worker</h3>
                <form onSubmit={handleRegistration} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Förnamn & Efternamn</label>
                        <input className="form-control" type="text" name="worker_name" placeholder="Förnamn & Efternamn" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Email</label>
                        <input className="form-control" type="email" name="worker_email" placeholder="Arbetarens Email" required />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Telefonnummer</label>
                        <input className="form-control" type="tel" name="worker_tel" placeholder="Ex: 0702457432" required /> {/* kan lägga till pattern på tel type */}
                    </div>

                    {/* {allCompanies ? 
                        <select className="js-example-basic-single" name="companyChoice">
                            {allCompanies.map(company => (
                                <option key={company.id} value={company.id}>{company.name}</option>
                            ))}
                        </select>
                    :  <></>} */}

                  {/*   {allUnits ? 
                        <select className="js-example-basic-single" name="companyChoice">
                            {allUnits.map(company => (
                                <option key={company.id} value={company.id}>{company.name}</option>
                            ))}
                        </select>
                    :  <></>} */}

                    {/* {allUnits ? 
                        <select className="js-example-basic-multiple" name="unitChoice[]" multiple="multiple">
                            {allUnits.map(unit => (
                                <option key={unit.id} value={unit.id}>{unit.name}</option>
                            ))}
                        </select>
                    :  <></>} */}
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
                            required
                        />
                        : <></>
                    }
                    <div className="mb-3">
                        <label className="form-label">Anställningstyp</label>
                        <select className="form-select" name="employment_type" required>
                            <option value="">Välj Anställningstyp</option>
                            <option value="Tillsvidareanställning">Tillsvidareanställning</option>
                            <option value="Visstidsanställning">Visstidsanställning</option>
                            <option value="Provanställning">Provanställning</option>
                            <option value="Konsult ">Konsult</option>
                            <option value="Timanställning">Timanställning</option>
                        </select>
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-primary w-50 me-2">Registrera</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    
    </> );

}
export default WorkerRegister;