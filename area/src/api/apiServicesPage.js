import axios from "axios"
import { useMutation } from "react-query"

const getAction = async () => {
  return await axios.post("/action/get_all")
}

export const useGetAction = () => {
  return useMutation(getAction)
}

const addAutomation = async (data) => {
  return await axios.post("/automation/add", JSON.stringify({ token: sessionStorage.getItem("token"), name: data.name, actions: data.actions }))
}

export const useAddAutomation = () => {
  return useMutation(addAutomation)
}

const editAutomation = async (data) => {
  return await axios.post("/automation/edit", JSON.stringify({automation_id: data.id, actions: data.actions, name: data.name}))
}

export const useEditAutomation = () => {
  return useMutation(editAutomation)
}
