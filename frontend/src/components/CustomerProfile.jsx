import { useContext, useEffect, useState } from "react";
import { AuthContext } from "../App";
import { Link } from "react-router-dom";

function CustomerProfile() {
    const { licenseValid, isAuth, setIsAuth, userId, userRole, userName } = useContext(AuthContext);
    const [customer, setCustomer] = useState(null);
    const [error, setError] = useState(null);
    const [isHovered, setIsHovered] = useState(false);

    useEffect(() => {
        const getWorker = async () => {
            try {
                const res = await fetch(`http://localhost/mini-axami/public/api/customer/${userId}`);
                const jsonRes = await res.json();

                if (jsonRes.error) {
                    setError(jsonRes.error);
                } else {
                    setCustomer(jsonRes.success);
                }
            } catch (error) {
                console.error("Fetch error:", error);
                setError("Kunde inte hämta kundinformation.");
            }
        };

        if (userId) getWorker();
    }, [userId]);
/* /revokeLicenseAccess/{id} */
    async function logout(){
            try{
                let res = await fetch('http://localhost/mini-axami/public/api/logout',{
                    method:'POST',
                    headers: { 'Content-Type': 'application/json' },
                }
                );
        
                if(!res.ok){
                    console.log('fetch failed');
                }
        
                let jsonRes = await res.json();
        
                if(jsonRes.success){
                    console.log(jsonRes.success);
                    setIsAuth(false);
                    navigate('/');
                }else{
                    console.log(jsonRes.error);
                }
        
                
            }catch(error){
                console.log('Triggered catch', error);
            }
        }

        async function revokeLicense(){
            if (window.confirm("Är du säker på att du vill avaktivera företagets license?")){
            try{
                let res = await fetch('http://localhost/mini-axami/public/api/revokeLicenseAccess/'+userId,{
                    method:'POST',
                    headers: { 'Content-Type': 'application/json' },
                }
                );
        
                if(!res.ok){
                    console.log('fetch failed');
                }
        
                let jsonRes = await res.json();
        
                if(jsonRes.success){
                    console.log(jsonRes.success);
                    setIsAuth(false);
                    /* navigate('/'); */
                }else{
                    console.log(jsonRes.error);
                }
        
                
            }catch(error){
                console.log('Triggered catch', error);
            }
        }
        }

    return (
            <div className="container p-5">
                {customer ? (
                <>                    
                    <div className="bg-light rounded shadow p-4">
                        <h3><strong>Namn:</strong> {customer.name}</h3>
                        <p><strong>Email:</strong> {customer.email}</p>
                        <p><strong>Organisationsnummer:</strong> {customer.identificationNumber}</p>
                        <p><strong>Kundtyp:</strong> {customer.customerType === 'company' ? "Företag" : "Privatperson"}</p>
                        <p><strong>Antal enheter:</strong> {customer.unit_amount}</p>
                        <p><strong>Antal arbetare:</strong> {customer.worker_amount}</p>
                        <h5>Din license:</h5>
                        <p><strong>Licensstatus:</strong> {licenseValid ? "✅ Aktiv" : "❌ Inaktiv"}</p>
                        <div title={customer.licenseKey} onMouseEnter={()=>setIsHovered(true)} onMouseLeave={()=>setIsHovered(false)} onTouchStart={()=>setIsHovered(true)} onTouchEnd={()=>setIsHovered(false)} className="bg-dark text-light p-2 rounded" style={{width:'fit-content'}}>
                            {isHovered ? (
                                <p>{customer.licenseKey}</p>
                            ) : <p>Visa licensenyckel</p>}
                        </div>  

                        {error && <p className="text-danger">{error}</p>}
                    </div>   
                    <button onClick={logout} className='btn bg-danger text-light'>Logout</button>
                    <button onClick={revokeLicense} className='btn bg-primary text-light'>Avregistrera License</button>
                    <Link className="btn btn-warning" to="/updateCustomer">Uppdatera Uppgifter</Link>
                </>     
        ) : (
            <h3 className="text-center">Laddar in...</h3>
        )}
        
        </div>
    );
}

export default CustomerProfile;
