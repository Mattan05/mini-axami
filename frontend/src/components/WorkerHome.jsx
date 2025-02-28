import React, {useContext} from 'react';
import { useEffect } from 'react';
import { LoadingContext, AuthContext } from '../App';
import { Link, useNavigate } from 'react-router-dom';


function WorkerHome() {
    const {isAuth, setIsAuth, userId, userRole, userName} = useContext(AuthContext);
    const navigate = useNavigate();

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
    
    return ( <>
        {isAuth ? 
        <>
            <h1 className='text-center text-primary'>Välkommen WORKER till Mini axami Underhållssystem</h1>
            <h3>Your name is <strong>{userName}</strong></h3>
            <h3>Your id is <strong>{userId}</strong></h3>
            <h3>Your role is <strong>{userRole}</strong></h3>
    
            <Link className="btn btn-success" to="##">Tilldelade uppgifter</Link>
            <Link className="btn btn-danger" to="/unitShow">Visa Units</Link>
            <button onClick={logout} className='btn bg-danger text-light'>Logout</button>
        </>
        :
           <></>
        }
        </> );
}

export default WorkerHome;