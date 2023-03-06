import axios from "axios"
import { useMutation } from "react-query"

//  function that make the request to get all actions 
const getAction = async () => {
  return await axios.post("/action/get_all")
}

// function that call the getAction function
export const useGetAction = () => {
  return useMutation(getAction)
}


// function that request to add an automation
const addAutomation = async (data) => {
  return await axios.post("/automation/add", JSON.stringify({ token: sessionStorage.getItem("token"), name: data.name, actions: data.actions }))
}

// function that call the addAutomation
export const useAddAutomation = () => {
  return useMutation(addAutomation)
}

// function that request to edit an automation
const editAutomation = async (data) => {
  return await axios.post("/automation/edit", JSON.stringify({automation_id: data.id, actions: data.actions, name: data.name}))
}

// function that call the editAutomation
export const useEditAutomation = () => {
  return useMutation(editAutomation)
}
