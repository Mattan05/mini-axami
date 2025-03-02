import { useState, useEffect, useContext } from "react";
import { LoadingContext, AuthContext } from "../App";
import { useParams, useNavigate, Link } from "react-router-dom";


function CustomerUpdate() {
    const {setLoading} = useContext(LoadingContext);
    const [customer, setCustomer] = useState([]);//eller array
    const navigate = useNavigate();
    const {userId, setUserName} = useContext(AuthContext);

    useEffect(()=>{
        const getCustomer = async () =>{
            try{
                const res = await fetch('http://localhost/mini-axami/public/api/customer/'+userId,{
                    method:'GET',
                    headers: { 'Content-Type': 'application/json'},
                });
                const jsonRes = await res.json();
    
                if(!res.ok) return console.log('fetch failed');
    
                console.log(jsonRes['success']);
    
                if(jsonRes['error']) return console.log(jsonRes.error || 'Server Error');
    
                setCustomer(jsonRes.success);
                
            }catch(error){
                console.log('Error ' + error);
            }
        }
        getCustomer();
    }, [userId]);

    async function handleUpdateCustomer(event) {
        event.preventDefault(); 
    
        let body = {};

        const newName = event.target.customer_name.value.trim();
        const newEmail = event.target.customer_email.value.trim();
        const newIdentificationNumber = event.target.identificationNumber.value.trim();
        const newCustomerType = event.target.customerType.value;
    
        if (newName && newName !== customer.name) {
            body.newName = newName;
        }
        if (newEmail && newEmail !== customer.email) {
            body.newEmail = newEmail;
        }
        if (newIdentificationNumber && newIdentificationNumber !== customer.phoneNmr) {
            body.newIdentificationNumber = newIdentificationNumber;
        }
        if (newCustomerType && newCustomerType !== customer.employmentType) {
            body.newCustomerType = newCustomerType;
        }

        console.log(body); 
    
        try {
            if (Object.keys(body).length > 0) { 
                const res = await fetch('http://localhost/mini-axami/public/api/updateCustomer/' + userId, {
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
                setCustomer(jsonRes.success);
                setUserName(customer.name); //BEHÖVS KSK EJ.
                navigate('/customerProfile')
            } else {
                console.log("Inga ändringar att uppdatera.");
            }
        } catch (error) {
            console.error("Error at catch: " + error);
        }
    }

    const handleCancel = () =>{
        navigate('/customerProfile');
    }
      /*    const newName = event.target.customer_name.value.trim();
        const newEmail = event.target.customer_email.value.trim();
        const newIdentificationNumber = event.target.identificationNumber.value.trim();
        const newCustomerType = event.target.customer_type.value; */
    return ( <>
        <div className="container d-flex justify-content-center align-items-center min-vh-100">
            <div className="card shadow-lg p-4" style={{ width: "400px", borderRadius: "12px" }}>
                <h3 className="text-center mb-4">Uppdatera Kund</h3>
                <form onSubmit={handleUpdateCustomer} onReset={handleCancel}>
                    <div className="mb-3">
                        <label className="form-label">Nytt namn</label>
                        <input className="form-control" type="text" name="customer_name" placeholder={customer.name} />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Ny Email</label>
                        <input className="form-control" type="email" name="customer_email" placeholder={customer.email} />
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Nytt Identifikationsnummer</label>
                        <input className="form-control" type="tel" name="identificationNumber" placeholder={customer.identificationNumber} /> {/* kan lägga till pattern på tel type */}
                    </div>

                    <div className="mb-3">
                        <label className="form-label">Välj ny Kundtyp</label>
                        <select className="form-select" name="customerType">
                            <option value="">Nuvarande: {customer.customerType === 'company' ? 'Företag' : 'Privatperson'}</option>
                            <option value="company">Företag</option>
                            <option value="private">Privat Person</option>
                        </select>
                    </div>

                    <div className="d-flex justify-content-between">
                        <button type="submit" className="btn btn-warning w-50 me-2">Uppdatera</button>
                        <button type="reset" className="btn btn-secondary w-50">Avbryt</button>
                    </div>
                </form>
            </div>
        </div>
    </> );
}

export default CustomerUpdate;