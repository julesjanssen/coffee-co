import { ref } from 'vue'

import { http } from '../shared/http'

interface SystemTaskOptions {
  pollInterval?: number
}

interface SystemTask {
  id: string
  status: 'pending' | 'processing' | 'completed' | 'failed'
  startedAt?: string
  completedAt?: string
  links: Record<string, string>
}

export function useTask() {
  const isRunning = ref(false)

  const startTask = async (
    callback: () => Promise<SystemTask> | SystemTask,
    options: SystemTaskOptions = {},
  ): Promise<SystemTask> => {
    const { pollInterval = 3_000 } = options
    const { promise, resolve, reject } = Promise.withResolvers<SystemTask>()

    isRunning.value = true

    try {
      // Execute the callback to get the initial task
      const initialTask = await callback()

      const pollTask = async (): Promise<void> => {
        try {
          const response = await http.get(initialTask.links.view)
          const task: SystemTask = response.data

          if (task.status === 'completed') {
            isRunning.value = false
            resolve(task)
          } else if (task.status === 'failed') {
            isRunning.value = false
            const error = new Error('Task failed')
            reject(error)
          } else {
            // Task still running, poll again
            setTimeout(pollTask, pollInterval)
          }
        } catch (error) {
          isRunning.value = false
          reject(error)
        }
      }

      // Start polling
      pollTask()
    } catch (error) {
      isRunning.value = false
      reject(error)
    }

    return promise
  }

  const downloadTaskResult = (task: SystemTask) => {
    const link = document.createElement('a')
    link.href = task.links.download
    link.download = ''
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }

  return {
    isRunning,
    downloadTaskResult,
    startTask,
  }
}
