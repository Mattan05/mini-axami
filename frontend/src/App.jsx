import { useState, useEffect, createContext } from 'react';
import { BrowserRouter as Router, Route, Routes, Link, useNavigate, Navigate, Outlet  } from 'react-router-dom';
import './Style.css';
/* import { fetchData } from './components/Api'; */
import Registration from './components/Registration';
import LicenseActivation from './components/LicenseActivation';
import Spinner from 'react-bootstrap/Spinner';
import Header from './components/Header';
import GuestHome from './components/GuestHome';
import Home from './components/Home';
import ValidatePassword from './components/ValidatePassword';
import Login from './components/Login';
import CreateUnit from './components/CreateUnit';
import ShowUnits from './components/ShowUnits';
import UnitPage from './components/UnitPage';
import UnitUpdate from './components/UnitUpdate';
import WorkerLogin from './components/WorkerLogin';
import WorkerRegister from './components/WorkerRegister';
import WorkerHome from './components/WorkerHome';
import ShowWorkers from './components/showWorkers';
import UpdateWorker from './components/UpdateWorker';
import CustomerProfile from './components/CustomerProfile';
import FaultyLicenseInfo from './components/FaultyLicenseInfo';
import CustomerUpdate from './components/CustomerUpdate';
import CreateUnitTask from './components/CreateUnitTask';
import ShowUnitTasks from './components/ShowUnitTasks';

export const LoadingContext = createContext();
export const AuthContext = createContext();

    
function App() {
    const [loading, setLoading] = useState(true);
    const [isAuth, setIsAuth] = useState(null);
    const [data, setData] = useState(null);
    const [error, setError] = useState(null);
    const [userId, setUserId] = useState(null);
    const [userRole, setUserRole] = useState([]);
    const [userName, setUserName] = useState(null);
    const [licenseValid, setLicenseValid] = useState(false);

    useEffect(() => {
        const checkSessionStatus = async () => {
            setLoading(true);
            try {
                const res = await fetch('http://localhost/mini-axami/public/api/auth', {
                    method: 'GET',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                });
    
                const sessionData = await res.json();
    
                if (sessionData.success) {
                    setIsAuth(true);
                    setUserId(sessionData.success.user_id);
                    setUserRole(sessionData.success.role);
                    setUserName(sessionData.success.name);
    
                    const licenseRes = await fetch('http://localhost/mini-axami/public/api/authLicense', {
                        method: 'GET',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json' },
                    });
    
                    const licenseData = await licenseRes.json();
               /*      console.log("licenseData");
                    console.log(licenseData); */
                    if(licenseData.errorLicense) {
                        setLicenseValid(false);
                        setIsAuth(false);
                        return;
                    }else if(licenseData.error){
                        console.log('Error License: ' + licenseData.error)
                    }
                    setLicenseValid(licenseData.success);
                } else {
                    setIsAuth(false);
                }
            } catch (error) {
                console.error("Fel vid sessionhantering:", error);
                setIsAuth(false);
            } finally {
                setLoading(false);
            }
        };
    
        checkSessionStatus();
    }, []);
    


   /* PROBLEM 2025-02-10: När jag loggar in och kommer till /home. Om jag skriver i url:en till "/" så kan jag inte gå tillbaka till "/home". Om jag kollar network för
   /api/auth retunerar den att jag är inloggad. fel på isauth någonstans. checksessionstatus eller login  */
    function PrivateRoute({ isAuth, licenseValid, userRole }) {
       /*  return isAuth ? <Outlet /> : <Navigate to="/" replace />; */
        if (!isAuth) return <Navigate to="/" replace />;
    
        if (!licenseValid){
            if(userRole.includes('ROLE_OWNER')) return <Navigate to="/activation" replace />; 
            if(userRole.includes('ROLE_WORKER')) return <Navigate to="/faultyLicenseInfo" replace />; 
        }
        return <Outlet />;
    }

    function OwnerRoute({ userRole }) {
        return userRole.includes('ROLE_OWNER') ? <Outlet /> : <Navigate to="/workerHome" replace />;
    }

    function WorkerRoute({ userRole }) {
        return userRole.includes('ROLE_WORKER') ? <Outlet /> : <Navigate to="/home" replace />;
    }

    return (
        
        <Router basename="/mini-axami/public">
            <h1>Loading: {JSON.stringify(loading)}</h1>
            <h2>License: {JSON.stringify(licenseValid)}</h2>
            <Header isAuth={isAuth} userRole={userRole}/>
            <main>
                <LoadingContext.Provider value={{loading, setLoading, error, setError}}>
                <AuthContext.Provider value={{licenseValid, setLicenseValid, setIsAuth, isAuth, userId, setUserId, userRole, setUserRole, userName, setUserName}}>
                    {loading ? (
                    <div className="d-flex justify-content-center align-items-center vh-100">
                            <Spinner animation="border" style={{ width: '3rem', height: '3rem' }} />
                        </div>
                    ) : /* error ? (
                        <p style={{ color: 'red' }}>Error: {error}</p>
                    ) : */ (
                       
                     /*    <GuestHome />, */



                     /* JAG BEHÖVER TÄNKA HUR JAG VILL GÖRA MED SESSIONS NÄR DET KOMMER TILL USERS OCKSÅ. HÅLLA KOLL MELLAN WORKER OCH CUSTOMER HUR VAD HMM
                        SEN ÄVEN FUNKAR INTE PASSWORD VERFICATION FÖR WORKER PÅGRUND AV VAD JAG TROR ATT SESSIONEN KRÅNGLAR PRECIS INNAN OCH I SERVER API ROUTE SÅ ANVÄNDER
                        DEN SESSIONEN FÖR ATT HÄMTA PASSWORDET. DET TROR JAG ÄR FÖR ATT PASSWORDVALIDATION INTE ÄR UNDER PRIVATE OCH OM MAN ÄR INLOGGAD SOM FÖRETAG SÅ BLIR DET 
                        KONSTIGT
                     */
                     
                        <Routes>
                            <Route path="/" element={<GuestHome  />} />
                            <Route path="/register" element={<Registration />} />
                            <Route path="/activation" element={<LicenseActivation />} />
                            <Route path="/faultyLicenseInfo" element={<FaultyLicenseInfo />} />
                           {/*  <Route path="/home" element={<Home />} /> */}
                            <Route path="/customerLogin" element={<Login />} />
                            <Route path="/workerLogin" element={<WorkerLogin />} />
                            <Route path="/passwordValidation" element={<ValidatePassword />} />
                            {/* <Route path="/unit/create" element={<CreateUnit />} />
                            <Route path="/unit/show" element={<ShowUnits />} /> */}

                            {/* Skyddade routes */}
                            <Route element={<PrivateRoute isAuth={isAuth} licenseValid={licenseValid} userRole={userRole}/>}>
                                <Route element={<OwnerRoute userRole={userRole} />}>
                                    <Route path="/home" element={<Home />} />
                                    <Route path="/unitCreate" element={<CreateUnit />} />
                                    <Route path="/unit/update/:id" element={<UnitUpdate />} />
                                    <Route path="/workerRegister" element={<WorkerRegister />} />
                                    <Route path="/updateWorker/:id" element={<UpdateWorker />} />
                                    <Route path="/customerProfile" element={<CustomerProfile />} />{/* /:id */}
                                    <Route path="/updateCustomer" element={<CustomerUpdate />} />{/* /:id */}
                                    <Route path="/createUnitTask" element={<CreateUnitTask />} />
                                    <Route path="/showUnitTasks" element={<ShowUnitTasks />} />{/* dfsdf */}
                                </Route>

                                <Route element={<WorkerRoute userRole={userRole} />}>
                                    <Route path="/workerHome" element={<WorkerHome />} />
                                </Route>

                                <Route path="/unitShow" element={<ShowUnits />} />
                                <Route path="/showUnitTasks" element={<ShowUnitTasks />} />{/* fdsfdsfsdf */}
                                <Route path="/unit/:id" element={<UnitPage />} />
                                <Route path="/showWorkers" element={<ShowWorkers />} /> {/* WORKERS SKA KUNNA SE SINA MEDARBETARE */}
                            </Route>
                        </Routes>
                            
                        /*   <button onClick="window.location.href='https://buy.stripe.com/test_bIYbJC56e8317Oo000'">
                                Checkout
                            </button> */
                    
                    )}
                </AuthContext.Provider> 
                </LoadingContext.Provider> 
            </main>
        </Router>
    );
}

export default App;
